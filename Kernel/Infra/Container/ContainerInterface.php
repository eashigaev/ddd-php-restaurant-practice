<?php

namespace Restaurant\Kernel\Infra\Container;

interface ContainerInterface
{
    public function make(string $contract);

    public function bind(string $contract, string $object);

    public function singleton(string $contract, string $object);
}