<?php

namespace Arbustofr;

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

use Arbustofr\App;
use Arbustofr\Framework;

class Container {
    public static function getContainerBuilder()
    {
        $container_builder = new DependencyInjection\ContainerBuilder();

        $container_builder->register('context', Routing\RequestContext::class);
        $container_builder->register('matcher', Routing\Matcher\UrlMatcher::class)
            ->setArguments([App::getRoutes(), new Reference('context')]);

        $container_builder->register('request_stack', HttpFoundation\RequestStack::class);
        $container_builder->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
        $container_builder->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class);

        $container_builder->register('listener.router', HttpKernel\EventListener\RouterListener::class)
            ->setArguments([new Reference('matcher'), new Reference('request_stack')]);
        $container_builder->register('listener.response', HttpKernel\EventListener\ResponseListener::class)
            ->setArguments(['%charset%']);
        $container_builder->setParameter('charset', 'UTF-8');

        $errorHandler = function (FlattenException $exception) {
            $msg = 'Something went wrong! ('.$exception->getMessage().')';

            return new HttpFoundation\Response($msg, $exception->getStatusCode());
        };
        $container_builder->register('listener.exception', HttpKernel\EventListener\ErrorListener::class)
            ->setArguments([$errorHandler]);

        $container_builder->register('dispatcher', EventDispatcher\EventDispatcher::class)
            ->addMethodCall('addSubscriber', [new Reference('listener.router')])
            ->addMethodCall('addSubscriber', [new Reference('listener.response')])
            ->addMethodCall('addSubscriber', [new Reference('listener.exception')]);

        $container_builder->register('framework', Framework::class)
            ->setArguments([
                new Reference('dispatcher'),
                new Reference('controller_resolver'),
                new Reference('request_stack'),
                new Reference('argument_resolver'),
            ]);

        return $container_builder;
    }
}
