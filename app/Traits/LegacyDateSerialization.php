<?php

namespace App\Traits;

use DateTimeInterface;

trait LegacyDateSerialization
{
    /**
     * Keep the Laravel 6 date format ('Y-m-d H:i:s') when models are
     * serialized to array / JSON (Laravel 7+ defaults to ISO-8601 UTC).
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }
}
