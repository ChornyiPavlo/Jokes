<?php

namespace App\Event;

class SampleMessage implements EventInterface
{
    public const NAME = 'sample_name';

    public function __construct(private string $content)
    {
    }

    public function getRoutingKey(): string
    {
        return self::NAME;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
