<?php

namespace Illuminatech\UrlTrailingSlash\Test;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Events\Dispatcher;
use Illuminatech\UrlTrailingSlash\CompiledRouteCollection;
use Illuminatech\UrlTrailingSlash\Router;

class CompiledRouteCollectionTest extends TestCase
{
    /**
     * @return Router test router instance.
     */
    protected function createRouter()
    {
        $container = new Container();

        $router = new Router(new Dispatcher, $container);

        $container->singleton(Registrar::class, function () use ($router) {
            return $router;
        });

        return $router;
    }

    public function testRestoreRouteFromComplied()
    {
        $router = $this->createRouter();

        $router->get('foo/bar/', function () {
            return 'with trailing slash';
        })->name('foo.bar');

        $compiled = $router->getRoutes()->compile();

        $routeCollection = new CompiledRouteCollection($compiled['compiled'], $compiled['attributes']);

        $routeCollection->setRouter($router);
        $routeCollection->setContainer(Container::getInstance());

        $route = $routeCollection->getByName('foo.bar');

        $this->assertTrue($route->hasTrailingSlash);
    }
}
