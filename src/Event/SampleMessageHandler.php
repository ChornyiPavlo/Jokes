<?php

namespace App\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SampleMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SampleMessage $message)
    {
        print_r($message->getContent() . ' handled');
    }
}