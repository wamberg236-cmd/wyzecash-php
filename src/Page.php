<?php

declare(strict_types=1);

namespace WyzeCash;

/**
 * Uma página de uma lista por cursor. `foreach` passa por todas as páginas, buscando a próxima só
 * quando precisa; `$pagina->items` são só os desta página.
 *
 * @template T
 *
 * @implements \IteratorAggregate<int, T>
 * @implements \ArrayAccess<string, mixed>
 */
final class Page implements \IteratorAggregate, \ArrayAccess
{
    /** @var list<T> Os itens desta página. */
    public readonly array $items;

    /** O cursor da próxima página (`null` na última). */
    public readonly ?string $nextCursor;

    /**
     * @param \Closure(?string): array<string, mixed> $fetch
     * @param array<string, mixed> $data A resposta inteira desta página (com os totais, quando a rota manda).
     */
    public function __construct(private readonly \Closure $fetch, public readonly array $data)
    {
        /** @var list<T> $items */
        $items = is_array($data['items'] ?? null) ? array_values($data['items']) : [];
        $this->items = $items;
        $next = $data['nextCursor'] ?? null;
        $this->nextCursor = is_string($next) && $next !== '' ? $next : null;
    }

    public function hasMore(): bool
    {
        return $this->nextCursor !== null;
    }

    /** @return self<T>|null A próxima página, ou `null` se esta é a última. */
    public function nextPage(): ?self
    {
        if ($this->nextCursor === null) {
            return null;
        }
        return new self($this->fetch, ($this->fetch)($this->nextCursor));
    }

    /** @return \Generator<int, T> */
    public function getIterator(): \Generator
    {
        $page = $this;
        $index = 0;
        while ($page !== null) {
            foreach ($page->items as $item) {
                yield $index++ => $item;
            }
            $page = $page->nextPage();
        }
    }

    /** @return \Generator<int, T> Igual ao `foreach`: todos os itens de todas as páginas. */
    public function autoPagingIterator(): \Generator
    {
        return $this->getIterator();
    }

    /**
     * Todos os itens numa lista só (cuidado com contas grandes: prefira o `foreach`).
     *
     * @return list<T>
     */
    public function toArray(?int $limit = null): array
    {
        $out = [];
        foreach ($this as $item) {
            $out[] = $item;
            if ($limit !== null && count($out) >= $limit) {
                break;
            }
        }
        return $out;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \LogicException('A página é só para leitura.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('A página é só para leitura.');
    }
}
