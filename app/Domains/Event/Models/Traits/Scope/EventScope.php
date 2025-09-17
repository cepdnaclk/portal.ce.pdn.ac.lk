<?php

namespace App\Domains\Event\Models\Traits\Scope;

/**
 * Class AnnouncementScope.
 */
trait EventScope
{
    /**
     * @param $query
     * @return mixed
     */
    public function scopeEnabled($query)
    {
        return $query->whereEnabled(true);
    }


    /**
     * @param $query
     * @return mixed
     */
    public function scopeGetUpcomingEvents($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('start_at', '>=', now());
            });
        });
    }

    public function scopeGetPastEvents($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('start_at', '<=', now());
            });
        });
    }
}
