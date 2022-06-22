<?php

namespace App\Messenger;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class Bus implements MessageBusInterface
{
    private \Symfony\Component\Messenger\MessageBusInterface $bus;

    public function __construct(\Symfony\Component\Messenger\MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        if (!$message instanceof \App\Event\EventInterface) {
            throw new \LogicException();
        }

        $this->bus->dispatch(Envelope::wrap($message, [
            new AmqpStamp($message->getRoutingKey(), AMQP_NOPARAM)
        ]));

        return $this->bus->dispatch($message, $stamps);
    }
}