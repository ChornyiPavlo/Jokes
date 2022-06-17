<?php

namespace App\messenger;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SampleMessangeHandler implements MessageHandlerInterface
{
    public function __invoke(SampleMessage $message)
    {
        print_r('Handler handled the message!');
    }
}