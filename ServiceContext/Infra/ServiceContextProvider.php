<?php

namespace Restaurant\ServiceContext\Infra;

use Restaurant\Kernel\Infra\Container\ContainerInterface;
use Restaurant\Kernel\Infra\Messaging\MessageBusInterface;
use Restaurant\ReservationContext\App\IntegrationEvents\ReservationContextTableSessionUpdateFailed;
use Restaurant\ServiceContext\App\OrderSaga;
use Restaurant\ServiceContext\Infra\Repositories\ProductRepository;
use Restaurant\ServiceContext\Infra\Repositories\ProductRepositoryInterface;

readonly class ServiceContextProvider
{
    public function __construct(
        private ContainerInterface  $container,
        private MessageBusInterface $messageBus
    )
    {
    }

    public function bootstrap(): string
    {
        $this->container->singleton(
            ProductRepositoryInterface::class, ProductRepository::class
        );
        $this->messageBus->listen(
            ReservationContextTableSessionUpdateFailed::class, OrderSaga::class
        );
    }
}