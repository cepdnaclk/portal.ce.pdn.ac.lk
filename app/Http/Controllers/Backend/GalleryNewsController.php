<?php

namespace App\Http\Controllers\Backend;

use App\Domains\News\Models\News;

class GalleryNewsController extends GalleryController
{

  public function getModel($id)
  {
    return News::findOrFail((int) $id);
  }
}
