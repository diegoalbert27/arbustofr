<?php

namespace Arbustofr;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

use Arbustofr\ResponseEvent;

class Framework
{
    private $dispatcher;
    private $matcher;
    private $controller_resolver;
    private $argument_resolver;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UrlMatcherInterface $matcher,
        ControllerResolverInterface $controller_resolver,
        ArgumentResolverInterface $argument_resolver
    )
    {
        $this->dispatcher = $dispatcher;
        $this->matcher = $matcher;
        $this->controller_resolver = $controller_resolver;
        $this->argument_resolver = $argument_resolver;
    }

    /**
     * handle
     *
     * @param  Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->controller_resolver->getController($request);
            $arguments = $this->argument_resolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            return new Response('Not found', 404);
        } catch (\Exception $exception) {
            return new Response('An error ocurred', 500);
        }

        // dispatch a response event
        $this->dispatcher->dispatch(new ResponseEvent($response, $request), 'response');

        return $response;
    }
}
