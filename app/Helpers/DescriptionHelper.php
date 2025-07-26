<?php

namespace App\Helpers;

class DescriptionHelper
{
  public static function process(string $description): string
  {
    $description = strip_tags($description);
    return str_replace('&nbsp;', ' ', $description);
  }
}