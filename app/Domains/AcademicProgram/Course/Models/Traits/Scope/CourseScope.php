<?php

namespace App\Domains\AcademicProgram\Course\Models\Traits\Scope;

/**
 * Class CourseScope.
 */
trait CourseScope
{
  /**
   * Scope a query to only include courses for a specific academic program.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $program
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeForProgram($query, $program)
  {
    return $query->where('academic_program', $program);
  }

  /**
   * Scope a query to only include courses for a specific semester.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $semesterId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeForSemester($query, $semesterId)
  {
    return $query->where('semester_id', $semesterId);
  }

  /**
   * Scope a query to only include courses of a specific type (Core, General Elective, Technical Elective).
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $type
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfType($query, $type)
  {
    return $query->where('type', $type);
  }

  /**
   * Scope a query to only include courses of a specific version.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $version
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfVersion($query, $version)
  {
    return $query->where('version', $version);
  }
}