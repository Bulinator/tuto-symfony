<?php

namespace App\EventSubscriber;

use App\Exception\EmptyBodyException;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::PRE_DESERIALIZE]
        ];
    }

    public function handleEmptyBody(RequestEvent $event)
    {
        $method = $event->getRequest()->getMethod();

        if (!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            return;
        }

        $data = $event->getRequest()->get('data');

        if (null === $data) {
            throw new EmptyBodyException();
        }
    }
}