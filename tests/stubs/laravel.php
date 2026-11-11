<?php

// O mínimo do Laravel para o LaravelTest rodar o provider e o middleware sem o framework
// instalado (com o Laravel no vendor, estas classes não são carregadas).

declare(strict_types=1);

namespace Symfony\Component\HttpFoundation {
    class ParameterBag
    {
        /** @param array<string, mixed> $values */
        public function __construct(public array $values = [])
        {
        }

        public function get(string $key, mixed $default = null): mixed
        {
            return $this->values[$key] ?? $default;
        }

        public function set(string $key, mixed $value): void
        {
            $this->values[$key] = $value;
        }
    }

    class HeaderBag extends ParameterBag
    {
        public function get(string $key, mixed $default = null): mixed
        {
            return $this->values[strtolower($key)] ?? $default;
        }
    }

    class Response
    {
        public function __construct(public mixed $content = '', public int $status = 200)
        {
        }
    }
}

namespace Illuminate\Http {
    use Symfony\Component\HttpFoundation\HeaderBag;
    use Symfony\Component\HttpFoundation\ParameterBag;

    class Request
    {
        public HeaderBag $headers;

        public ParameterBag $attributes;

        /** @param array<string, string> $headers */
        public function __construct(private string $content, array $headers)
        {
            $this->headers = new HeaderBag(array_change_key_case($headers));
            $this->attributes = new ParameterBag();
        }

        public function getContent(): string
        {
            return $this->content;
        }
    }

    class Response extends \Symfony\Component\HttpFoundation\Response
    {
    }

    class JsonResponse extends \Symfony\Component\HttpFoundation\Response
    {
        public function __construct(mixed $data = null, int $status = 200)
        {
            parent::__construct(json_encode($data), $status);
        }
    }
}

namespace Illuminate\Contracts\Config {
    interface Repository
    {
        public function get(string $key, mixed $default = null): mixed;

        public function set(string $key, mixed $value): void;
    }
}

namespace Illuminate\Contracts\Foundation {
    interface Application
    {
    }
}

namespace Illuminate\Routing {
    class Router
    {
        /** @var array<string, string> */
        public array $aliases = [];

        public function aliasMiddleware(string $name, string $class): static
        {
            $this->aliases[$name] = $class;
            return $this;
        }
    }
}

namespace Illuminate\Support {
    abstract class ServiceProvider
    {
        /** @var array<string, array<string, string>> */
        public static array $published = [];

        public function __construct(protected mixed $app)
        {
        }

        protected function mergeConfigFrom(string $path, string $key): void
        {
            $config = $this->app->make('config');
            $config->set($key, array_merge(require $path, $config->get($key, [])));
        }

        /** @param array<string, string> $paths */
        protected function publishes(array $paths, mixed $groups = null): void
        {
            static::$published[(string) $groups] = $paths;
        }
    }
}

namespace {
    if (!function_exists('env')) {
        function env(string $key, mixed $default = null): mixed
        {
            $value = getenv($key);
            return $value === false ? $default : $value;
        }
    }
}
