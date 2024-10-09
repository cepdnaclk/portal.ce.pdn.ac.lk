<?php

namespace App\Domains\AcademicProgram\Course\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\CourseFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Domains\AcademicProgram\AcademicProgram;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\AcademicProgram\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\AcademicProgram\Course\Models\Traits\Scope\CourseScope;

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

    public static function getILOTemplate(): array
    {
        // TODO Get the list from Taxonomies
        return [
            'general' => [],
            'knowledge' => [],
            'skills' => [],
            'attitudes' => [],
        ];
    }
    public static function getMarksAllocation(): array
    {
        // TODO Get the list from Taxonomies
        return [
            'practicals' => null,
            'tutorials' => null,
            'quizzes' => null,
            'projects' => null,
            'participation' => null,
            'mid_exam' => null,
            'end_exam' => null,
        ];
    }

    public static function getTimeAllocation(): array
    {
        // TODO Get the list from Taxonomies
        return [
            'lecture' => null,
            'tutorial' => null,
            'practical' => null,
            'design' => null,
            'assignment' => null,
            'independent_learning' => null
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

    /**
     * Get the prerequisites for the course.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    /**
     * Get the courses where this course is a prerequisite.
     */
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_id', 'course_id');
    }

    protected static function newFactory()
    {
        return CourseFactory::new();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('course') // Set a custom log name
            ->setDescriptionForEvent(fn(string $eventName) => "This course has been {$eventName}.")
        ;
    }
}