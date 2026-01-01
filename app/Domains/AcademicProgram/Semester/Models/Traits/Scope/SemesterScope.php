<?php

namespace App\Domains\AcademicProgram\Semester\Models\Traits\Scope;

/**
 * Class SemesterScope.
 */
trait SemesterScope
{
  /**
   * Scope a query to only include semesters of a specific version.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $version
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfVersion($query, $version)
  {
    return $query->where('version', $version);
  }

  /**
   * Scope a query to only include semesters for a specific academic program.
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $program
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeForProgram($query, $program)
  {
    return $query->where('academic_program', $program);
  }
}