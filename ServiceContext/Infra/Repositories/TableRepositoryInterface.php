<?php

namespace Restaurant\ServiceContext\Infra\Repositories;

use Restaurant\ServiceContext\Domain\Table;

interface TableRepositoryInterface
{
    public function ofId(string $id): ?Table;
}