<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Support\Str;

/**
 * Router is an enhanced version of {@see \Illuminate\Routing\Router}, which allows routes with trailing slashes definition.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Dispatcher $events, ?Container $container = null)
    {
        parent::__construct($events, $container);

        $this->routes = new RouteCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function prefix($uri): string
    {
        $result = parent::prefix($uri);

        if (Str::endsWith($uri, '/')) {
            return $result.'/';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * {@inheritdoc}
     */
    public function resource($name, $controller, array $options = [])
    {
        if (Str::endsWith($name, '/')) {
            $name = rtrim($name, "/ \t\n\r\0\x0B");
            if (! isset($options['trailingSlashOnly'])) {
                $options['trailingSlashOnly'] = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
            }
        }

        if (empty($options['trailingSlashOnly']) && empty($options['trailingSlashExcept'])) {
            return parent::resource($name, $controller, $options);
        }

        if ($this->container && $this->container->bound(BaseResourceRegistrar::class)) {
            $registrar = $this->container->make(BaseResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        return new PendingResourceRegistration(
            $registrar, $name, $controller, $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setCompiledRoutes(array $routes)
    {
        $this->routes = (new CompiledRouteCollection($routes['compiled'], $routes['attributes']))
            ->setRouter($this)
            ->setContainer($this->container);

        $this->container->instance('routes', $this->routes);
    }
}
