<?php

namespace Illuminatech\UrlTrailingSlash\Test;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminatech\UrlTrailingSlash\Route;
use Illuminatech\UrlTrailingSlash\UrlGenerator;

class UrlGeneratorTest extends TestCase
{
    public function testBasicGeneration()
    {
        $urlGenerator = new UrlGenerator(
            new RouteCollection(),
            Request::create('http://www.example.com/')
        );

        $this->assertEquals('http://www.example.com/foo/bar', $urlGenerator->to('foo/bar'));
        $this->assertEquals('http://www.example.com/foo/bar/', $urlGenerator->to('foo/bar/'));
        $this->assertEquals('http://www.example.com/foo/bar/', $urlGenerator->to('foo/bar//'));
    }

    public function testBasicRouteGeneration()
    {
        $urlGenerator = new UrlGenerator(
            $routes = new RouteCollection(),
            Request::create('http://www.example.com/')
        );

        $routes->add(new Route(['GET'], '', ['as' => 'home.no.slash']));
        $routes->add(new Route(['GET'], '/', ['as' => 'home.with.slash']));
        $routes->add(new Route(['GET'], 'foo/bar', ['as' => 'plain.no.slash']));
        $routes->add(new Route(['GET'], 'foo/bar/', ['as' => 'plain.with.slash']));
        $routes->add(new Route(['GET'], 'foo/bar/{baz}/breeze/{boom}', ['as' => 'param.no.slash']));
        $routes->add(new Route(['GET'], 'foo/bar/{baz}/breeze/{boom}/', ['as' => 'param.with.slash']));
        $routes->add(new Route(['GET'], 'foo/bar/{option?}', ['as' => 'optional.param.no.slash']));
        $routes->add(new Route(['GET'], 'foo/bar/{option?}/', ['as' => 'optional.param.with.slash']));

        $this->assertEquals('/', $urlGenerator->route('home.no.slash', [], false));
        $this->assertEquals('/', $urlGenerator->route('home.with.slash', [], false));
        $this->assertEquals('/foo/bar', $urlGenerator->route('plain.no.slash', [], false));
        $this->assertEquals('/foo/bar/', $urlGenerator->route('plain.with.slash', [], false));
        $this->assertEquals('/foo/bar/one/breeze/two?extra=three', $urlGenerator->route('param.no.slash', ['one', 'two', 'extra' => 'three'], false));
        $this->assertEquals('/foo/bar/one/breeze/two/?extra=three', $urlGenerator->route('param.with.slash', ['one', 'two', 'extra' => 'three'], false));
        $this->assertEquals('/foo/bar', $urlGenerator->route('optional.param.no.slash', [], false));
        $this->assertEquals('/foo/bar/one', $urlGenerator->route('optional.param.no.slash', ['one'], false));
        $this->assertEquals('/foo/bar/', $urlGenerator->route('optional.param.with.slash', [], false));
        $this->assertEquals('/foo/bar/one/', $urlGenerator->route('optional.param.with.slash', ['one'], false));
    }
}
