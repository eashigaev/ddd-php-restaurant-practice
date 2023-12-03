<?php

namespace Restaurant\ServiceContext\Domain;

enum OrderStatus: string
{
    case SERVING = "SERVING";
    case PAYING = "PAYING";
    case COMPLETED = "COMPLETED";
}