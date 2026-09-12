<?php

namespace App\Domains\Profile\Listeners;

use App\Domains\Profile\Events\ProfileUpdated;

/**
 * Class ProfileEventListener.
 */
class ProfileEventListener
{
  /**
   * Keep the linked portal account's display name in step with the
   * preferred long name, whichever flow (admin, wizard, self-service,
   * sync, merge) updated the profile.
   *
   * @param $event
   */
  public function onUpdated($event)
  {
    $profile = $event->profile;

    if (! $profile->wasChanged('preferred_long_name') || blank($profile->preferred_long_name)) {
      return;
    }

    $profile->user()->first()?->update(['name' => $profile->preferred_long_name]);
  }

  /**
   * Register the listeners for the subscriber.
   *
   * @param  \Illuminate\Events\Dispatcher  $events
   */
  public function subscribe($events)
  {
    $events->listen(
      ProfileUpdated::class,
      'App\Domains\Profile\Listeners\ProfileEventListener@onUpdated'
    );
  }
}
