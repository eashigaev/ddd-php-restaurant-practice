<?php

namespace Restaurant\Kernel\Infra\Messaging;

interface MessageBusInterface
{
    public function emit(object $message);

    public function listen(string $type, string $handler);
}