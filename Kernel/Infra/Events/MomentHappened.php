<?php

namespace Restaurant\Kernel\Infra\Events;

use DateTime;

class MomentHappened
{
    public DateTime $momentAt;

    public static function from(DateTime $momentAt): static
    {
        $self = new static();
        $self->momentAt = $momentAt;
        return $self;
    }
}