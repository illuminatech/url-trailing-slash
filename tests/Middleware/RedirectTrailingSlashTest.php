<?php

namespace Illuminatech\UrlTrailingSlash\Test\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\RouteCollection;
use Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash;
use Illuminatech\UrlTrailingSlash\Route;
use Illuminatech\UrlTrailingSlash\Test\TestCase;
use Illuminatech\UrlTrailingSlash\UrlGenerator;

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

    /**
     * Data provider for {@see testRedirect()}
     *
     * @return array test data.
     */
    public static function dataProviderRedirect(): array
    {
        return [
            [
                'foo/bar/',
                'http://example.com/foo/bar',
                'http://example.com/foo/bar/'
            ],
            [
                'foo/bar',
                'http://example.com/foo/bar/',
                'http://example.com/foo/bar',
            ],
            [
                'foo/bar/',
                'http://example.com/foo/bar?name=value',
                'http://example.com/foo/bar/?name=value',
            ],
            [
                'foo/bar',
                'http://example.com/foo/bar/?name=value',
                'http://example.com/foo/bar?name=value',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderRedirect
     *
     * @param  string  $routeUri
     * @param  string  $requestUri
     * @param  string  $redirectUri
     */
    public function testRedirect(string $routeUri, string $requestUri, string $redirectUri)
    {
        $middleware = new RedirectTrailingSlash($this->createContainer());

        $request = $this->createRequest($this->createRoute($routeUri), $requestUri);

        /* @var $response RedirectResponse */
        $response = $middleware->handle($request, $this->createDummyRequestHandler());

        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertSame($redirectUri, $response->headers->get('Location'));
    }

    /**
     * Data provider for {@see testNoRedirect()}
     *
     * @return array test data.
     */
    public static function dataProviderNoRedirect(): array
    {
        return [
            [
                'foo/bar/',
                'http://example.com/foo/bar/',
            ],
            [
                'foo/bar',
                'http://example.com/foo/bar',
            ],
            [
                'foo/bar/',
                'http://example.com/foo/bar/?name=value',
            ],
            [
                'foo/bar',
                'http://example.com/foo/bar?name=value',
            ],
            [
                '/',
                'http://example.com/',
            ],
            [
                '',
                'http://example.com',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderNoRedirect
     *
     * @param  string  $routeUri
     * @param  string  $requestUri
     */
    public function testNoRedirect(string $routeUri, string $requestUri)
    {
        $middleware = new RedirectTrailingSlash($this->createContainer());

        $request = $this->createRequest($this->createRoute($routeUri), $requestUri);

        $response = $middleware->handle($request, $this->createDummyRequestHandler());

        $this->assertSame($request, $response);
    }
}
