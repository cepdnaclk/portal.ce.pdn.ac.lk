<?php

namespace App\Domains\Course\Models;

use App\Domains\Course\Models\Traits\Scope\CourseScope;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Course.
 */
class Course extends Model
{
    use CourseScope,
        HasFactory,
        LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'semester_id',
        'academic_program',
        'version',
        'name',
        'credits',
        'type',
        'content',
        'objectives',
        'time_allocation',
        'marks_allocation',
        'ilos',
        'urls',
        'references',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'academic_program' => 'string',
        'type' => 'string',
        'objectives' => 'json',
        'time_allocation' => 'json',
        'marks_allocation' => 'json',
        'ilos' => 'json',
        'urls' => 'json',
        'references' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return CourseFactory::new();
    }
}