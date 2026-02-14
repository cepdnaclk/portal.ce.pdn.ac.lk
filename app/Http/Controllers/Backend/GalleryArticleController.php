<?php

namespace App\Http\Controllers\Backend;

use App\Domains\ContentManagement\Models\Article;

class GalleryArticleController extends GalleryController
{
  public function getModel($id)
  {
    return Article::findOrFail((int) $id);
  }
}