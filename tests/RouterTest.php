<?php

namespace Illuminatech\UrlTrailingSlash\Test;

use Illuminate\Http\Request;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminatech\UrlTrailingSlash\Route;
use Illuminatech\UrlTrailingSlash\Router;
use Illuminate\Contracts\Routing\Registrar;

class RouterTest extends TestCase
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

    public function testAddRoute()
    {
        $router = $this->createRouter();

        $router->get('foo/bar/', function () {
            return 'with trailing slash';
        });

        $route = $router->getRoutes()->getRoutes()[0];

        $this->assertTrue($route instanceof Route);
        $this->assertTrue($route->hasTrailingSlash);

        $router->get('bar/foo', function () {
            return 'without trailing slash';
        });

        $route = $router->getRoutes()->getRoutes()[1];

        $this->assertTrue($route instanceof Route);
        $this->assertFalse($route->hasTrailingSlash);

        $router->get('/', function () {
            return 'home with trailing slash';
        });

        $route = $router->getRoutes()->getRoutes()[2];

        $this->assertTrue($route instanceof Route);
        $this->assertTrue($route->hasTrailingSlash);
    }

    public function testMatch()
    {
        $router = $this->createRouter();

        $router->get('foo/bar/', function () {
            return 'with trailing slash';
        });

        $this->assertEquals('with trailing slash', $router->dispatch(Request::create('foo/bar/', 'GET'))->getContent());
        $this->assertEquals('with trailing slash', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());
    }

    /**
     * @depends testAddRoute
     */
    public function testAddResourceRoute()
    {
        $router = $this->createRouter();
        $router->resource('foo', 'NoSlashController');
        $routes = $router->getRoutes()->getRoutes();

        $this->assertTrue($routes[0] instanceof Route);
        $this->assertFalse($routes[0]->hasTrailingSlash);

        $router = $this->createRouter();
        $router->resource('foo/', 'WithSlashController');
        $routes = $router->getRoutes()->getRoutes();

        foreach ($routes as $route) {
            $this->assertTrue($route instanceof Route);
            $this->assertTrue($route->hasTrailingSlash, 'No trailing slash for '.$route->uri);
        }

        $router = $this->createRouter();
        $router->resource('foo', 'WithSlashController', ['only' => ['index', 'show'], 'trailingSlashOnly' => 'show']);
        $routes = $router->getRoutes()->getRoutes();
        $this->assertFalse($routes[0]->hasTrailingSlash);
        $this->assertTrue($routes[1]->hasTrailingSlash);

        $router = $this->createRouter();
        $router->resource('foo', 'WithSlashController', ['only' => ['index', 'show'], 'trailingSlashExcept' => 'show']);
        $routes = $router->getRoutes()->getRoutes();
        $this->assertTrue($routes[0]->hasTrailingSlash);
        $this->assertFalse($routes[1]->hasTrailingSlash);

        $router = $this->createRouter();
        $router->resource('foo', 'WithSlashController', ['only' => ['index', 'show'], 'trailingSlashOnly' => 'index', 'trailingSlashExcept' => 'show']);
        $routes = $router->getRoutes()->getRoutes();
        $this->assertTrue($routes[0]->hasTrailingSlash);
        $this->assertFalse($routes[1]->hasTrailingSlash);

        $router = $this->createRouter();
        $router->resource('foo', 'WithSlashController', ['only' => ['index', 'show'], 'trailingSlashOnly' => ['index', 'show'], 'trailingSlashExcept' => 'show']);
        $routes = $router->getRoutes()->getRoutes();
        $this->assertTrue($routes[0]->hasTrailingSlash);
        $this->assertFalse($routes[1]->hasTrailingSlash);
    }
}
