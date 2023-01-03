<?php

namespace Arbustofr\Tests;

use PHPUnit\Framework\TestCase;

use Arbustofr\Controllers\ByeController;
use Arbustofr\Framework;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FrameworkTest extends TestCase
{
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());

        $this->assertEquals(404, $response->getStatusCode());
    }

    private function getFrameworkForException($exception)
    {
        $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $matcher = $this->getMock(Routing\Matcher\UrlMatcherInterface::class);

        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception));

        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock(Routing\RequestContext::class)));

        $controller_resolver = $this->createMock(ControllerResolverInterface::class);
        $argument_resolver = $this->createMock(ArgumentResolverInterface::class);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher
            ->expects($this->once())
            ->method('addSubscriber')
            ->will($this->returnValue($this->createMock(Arbustofr\GoogleListener::class)));

        $dispatcher
            ->expects($this->once())
            ->method('dispatch');

        return new Framework($dispatcher, $matcher, $controller_resolver, $argument_resolver);
    }

    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());

        $response = $framework->handle(new Request());

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testControllerResponse()
    {
        $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $matcher = $this->getMock(Routing\Matcher\UrlMatcherInterface::class);

        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->returnValue([
                '_route' => 'bye/{name}',
                'name' => 'Diego',
                '_controller' => [new ByeController(), 'index'],
            ]));

        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock(Routing\RequestContext::class)));

        $controller_resolver = new ControllerResolver();
        $argument_resolver = new ArgumentResolver();

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher
            ->expects($this->once())
            ->method('dispatch');

        $framework = new Framework($dispatcher, $matcher, $controller_resolver, $argument_resolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Goodbye Diego', $response->getContent());
    }
}

