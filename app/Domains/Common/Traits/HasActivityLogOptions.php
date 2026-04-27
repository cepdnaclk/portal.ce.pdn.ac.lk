<?php

namespace App\Domains\Common\Traits;

use Spatie\Activitylog\LogOptions;

/**
 * Shared activity log configuration for models.
 */
trait HasActivityLogOptions
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
}
