<?php

namespace App\Domains\AcademicProgram\Course\Models;

use App\Domains\Auth\Models\User;
use App\Domains\AcademicProgram\AcademicProgram;
use App\Domains\AcademicProgram\Course\Models\Traits\Scope\CourseScope;
use App\Domains\AcademicProgram\Semester\Models\Semester;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Course.
 */
class Course extends AcademicProgram
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

    public function academicProgram()
    {
        return $this->getAcademicPrograms()[$this->academic_program];
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function version()
    {
        return $this->getVersions()[$this->version];
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