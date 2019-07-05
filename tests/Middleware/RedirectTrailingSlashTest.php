<?php

namespace Illuminatech\UrlTrailingSlash\Test\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\RouteCollection;
use Illuminatech\UrlTrailingSlash\Route;
use Illuminatech\UrlTrailingSlash\Test\TestCase;
use Illuminatech\UrlTrailingSlash\UrlGenerator;
use Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash;

class RedirectTrailingSlashTest extends TestCase
{
    protected function createContainer(): Container
    {
        $container = new Container();

        $container->singleton('url', function (Container $app) {
            return new UrlGenerator(
                new RouteCollection(),
                Request::create('http://www.example.com/')
            );
        });

        $container->singleton('redirect', function (Container $app) {
            return new Redirector($app->make('url'));
        });

        return $container;
    }

    /**
     * Creates dummy request handler for the middleware.
     *
     * @return \Closure
     */
    protected function createDummyRequestHandler(): Closure
    {
        return function (Request $request) {
            return $request;
        };
    }

    /**
     * Creates test HTTP request instance with route bound to it.
     *
     * @param  Route  $route
     * @param  string  $uri
     * @param  string  $method
     * @return Request
     */
    protected function createRequest(Route $route, string $uri, string $method = 'GET'): Request
    {
        $request = Request::create($uri, $method);

        $route->bind($request);

        $request->setRouteResolver(function () use ($route, $request) {
            return $route;
        });

        return $request;
    }

    protected function createRoute($uri)
    {
        return new Route(['GET'], $uri, function () {
            return true;
        });
    }

    public function testRedirect()
    {
        /* @var $response RedirectResponse */

        $middleware = new RedirectTrailingSlash($this->createContainer());

        $request = $this->createRequest($this->createRoute('foo/bar/'), 'http://example.com/foo/bar');

        $response = $middleware->handle($request, $this->createDummyRequestHandler());
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertSame('http://example.com/foo/bar/', $response->headers->get('Location'));

        $request = $this->createRequest($this->createRoute('foo/bar'), 'http://example.com/foo/bar/');

        $response = $middleware->handle($request, $this->createDummyRequestHandler());
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertSame('http://example.com/foo/bar', $response->headers->get('Location'));

        $request = $this->createRequest($this->createRoute('foo/bar/'), 'http://example.com/foo/bar?name=value');

        $response = $middleware->handle($request, $this->createDummyRequestHandler());
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertSame('http://example.com/foo/bar/?name=value', $response->headers->get('Location'));

        $request = $this->createRequest($this->createRoute('foo/bar'), 'http://example.com/foo/bar/?name=value');

        $response = $middleware->handle($request, $this->createDummyRequestHandler());
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertSame('http://example.com/foo/bar?name=value', $response->headers->get('Location'));
    }
}
