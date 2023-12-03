<?php

namespace Restaurant\ServiceContext\Domain;

class Table
{
    public string $id;
    public string $sessionId;
    public bool $isRestricted;

    public static function from(string $id, string $sessionId, bool $isRestricted): static
    {
        $self = new static;
        $self->id = $id;
        $self->sessionId = $sessionId;
        $self->isRestricted = $isRestricted;
        return $self;
    }
}