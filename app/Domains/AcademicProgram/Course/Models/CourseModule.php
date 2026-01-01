<?php

namespace App\Domains\AcademicProgram\Course\Models;

use App\Domains\AcademicProgram\Course\Models\Traits\Scope\CourseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class CourseModule.
 */
class CourseModule extends Model
{
  use CourseScope,
    HasFactory,
    LogsActivity;

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $table = 'course_modules';

  /**
   * @var string[]
   */
  protected $fillable = [
    'course_id',
    'topic',
    'description',
    'time_allocation',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'course_id' => 'integer',
    'topic' => 'string',
    'description' => 'string',
    'time_allocation' => 'json',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'created_by' => 'integer',
    'updated_by' => 'integer',
  ];

  public function course()
  {
    return $this->belongsTo(Course::class);
  }
}