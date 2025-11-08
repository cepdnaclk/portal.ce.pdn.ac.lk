<?php

namespace App\Http\Controllers\Backend;

use App\Domains\Event\Models\Event;

class GalleryEventController extends GalleryController
{

  public function getModel($id)
  {
    return Event::findOrFail((int) $id);
  }
}
