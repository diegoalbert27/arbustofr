<?php

namespace Arbustofr;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GoogleListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['response' => 'onResponse'];
    }
    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        if (
            $response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'text/plain'))
            || 'html' !== $event->getRequest()->getRequestFormat()
        ) {
            return;
        }

        $response->setContent($response->getContent() . ' GA CODE');
    }
}
