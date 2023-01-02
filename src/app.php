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

        $routes->add('hello', new Route('/hello/{name}', [
            'name' => 'World',
            '_controller' => function ($request) {
                $response = render_template($request);
                $response->headers->set('Content-type', 'text/plain');

                return $response;
            }
        ]));

        $routes->add('bye', new Route('/bye/{name}', [
            'name' => 'World',
            '_controller' => 'Arbustofr\Controllers\ByeController::index'
        ]));

        return $routes;
    }
}
