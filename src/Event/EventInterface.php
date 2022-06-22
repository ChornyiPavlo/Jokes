<?php

namespace App\Event;

interface EventInterface
{
    public function getRoutingKey(): string;
}