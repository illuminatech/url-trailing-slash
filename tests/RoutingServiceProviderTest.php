<?php

namespace Illuminatech\UrlTrailingSlash\Test;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\RoutingServiceProvider as BaseRoutingServiceProvider;
use Illuminatech\UrlTrailingSlash\Router;
use Illuminatech\UrlTrailingSlash\RoutingServiceProvider;
use Illuminatech\UrlTrailingSlash\UrlGenerator;

class RoutingServiceProviderTest extends TestCase
{
    protected function readProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionObject($object);

        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    public function testRegister()
    {
        $container = new Container();

        $container->instance('config', new Repository());
        $container->instance('request', new Request());

        (new EventServiceProvider($container))->register();
        (new BaseRoutingServiceProvider($container))->register();

        (new RoutingServiceProvider($container))->register();

        $router = $container->make('router');
        $this->assertInstanceOf(Router::class, $router);

        $urlGenerator = $container->make('url');
        $this->assertInstanceOf(UrlGenerator::class, $urlGenerator);

        $this->assertInstanceOf(\Closure::class, $this->readProtectedProperty($urlGenerator, 'sessionResolver'));
        $this->assertInstanceOf(\Closure::class, $this->readProtectedProperty($urlGenerator, 'keyResolver'));
    }
}
