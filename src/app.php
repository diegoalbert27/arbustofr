<?php
namespace Arbustofr;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class App
{
    /**
     * getRoutes
     *
     * @return RouteCollection
     */
    public static function getRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add('hello', new Route('/hello/{name}', ['name' => 'World']));
        $routes->add('bye', new Route('/bye'));

        return $routes;
    }
}
