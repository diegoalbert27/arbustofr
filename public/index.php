<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Arbustofr\GoogleListener;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * render_template
 *
 * @param  mixed $request
 * @return Response
 */
function render_template(Request $request): Response
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__ . '/../src/pages/%s.php', $_route);

    return new Response(ob_get_clean());
}

$request = Request::createFromGlobals();

$routes = Arbustofr\App::getRoutes();

$context = new RequestContext();
$matcher = new UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new GoogleListener());

$controller_resolver = new ControllerResolver();
$argument_resolver = new ArgumentResolver();

$framework = new Arbustofr\Framework($dispatcher, $matcher, $controller_resolver, $argument_resolver);
$response = $framework->handle($request);

$response->send();
