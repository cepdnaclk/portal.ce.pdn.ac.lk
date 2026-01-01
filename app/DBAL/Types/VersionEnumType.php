<?php

namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Domains\AcademicProgram\Course\Models\Course;

class VersionEnumType extends Type
{
  const ENUM = 'enum';

  public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
  {
    return "ENUM('" . implode("','", array_keys(Course::getVersions())) . "')";
  }

  public function convertToPHPValue($value, AbstractPlatform $platform)
  {
    return $value;
  }

  public function getName()
  {
    return self::ENUM;
  }
}