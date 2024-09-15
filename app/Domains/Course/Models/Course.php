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

    protected $table = 'courses';

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
        'references' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getMarksAllocation(): array
    {
        return [
            'practicals' => null,
            'project' => null,
            'mid_exam' => null,
            'end_exam' => null
        ];
    }

    public static function getTimeAllocation(): array
    {
        return [
            'lecture' => null,
            'tutorial' => null,
            'practical' => null,
            'assignment' => null
        ];
    }

    public static function getTypes(): array
    {
        return [
            'Core' => 'Core',
            'GE' => 'General Elective',
            'TE' => 'Technical Elective'
        ];
    }

    public static function getAcademicPrograms(): array
    {
        return [
            'undergraduate' => 'Undergraduate',
            'postgraduate' => 'Postgraduate'
        ];
    }

    public static function getVersions(): array
    {
        return [
            1 => 'Current Curriculum',
            2 => 'Curriculum - Effective from E22'
        ];
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    protected static function newFactory()
    {
        return CourseFactory::new();
    }
}
