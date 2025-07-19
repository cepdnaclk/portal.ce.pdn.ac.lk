<?php

namespace App\Domains\AcademicProgram\Course\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\CourseFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\AcademicProgram\AcademicProgram;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\AcademicProgram\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\AcademicProgram\Course\Models\Traits\Scope\CourseScope;
use App\Domains\Taxonomy\Models\TaxonomyTerm;

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
        'teaching_methods',
        'faq_page',
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


    const CACHE_DURATION = 3600; // Cache duration in seconds (1 hour)


    /*
    * Get the list of academic programs.
    * This is used to get the list of academic programs
    *
    * @return array
    */
    public static function getILOTemplate(): array
    {
        return  cache()->remember(
            'ilo_templates',
            self::CACHE_DURATION,
            function () {
                $courseILOs = TaxonomyTerm::where('code', 'course_ilos')->firstOrFail();
                $ilos = [];
                foreach ($courseILOs->children as $ilo) {
                    $ilos[$ilo->code] = [];
                }
                return $ilos;
            }
        );
    }


    /*
    * Get the list of academic programs.
    * This is used to get the list of academic programs
    * @return array
    */
    public static function getMarksAllocation(): array
    {
        $marksAllocation =  cache()->remember(
            'marks_allocation_templates',
            self::CACHE_DURATION,
            function () {
                $allocation = TaxonomyTerm::where('code', 'mark_allocations')->firstOrFail();
                $ilos = [];
                foreach ($allocation->children as $ilo) {
                    $marksAllocation[$ilo->code] = null;
                }
                return $marksAllocation;
            }
        );

        return $marksAllocation;
    }

    /**
     * Get the time allocation templates.
     * This is used to get the time allocation templates
     *
     * @return array
     */
    public static function getTimeAllocation(): array
    {
        $timeAllocation =  cache()->remember(
            'time_allocation_templates',
            self::CACHE_DURATION,
            function () {
                $allocation = TaxonomyTerm::where('code', 'time_allocations')->firstOrFail();
                $timeAllocation = [];
                foreach ($allocation->children as $ilo) {
                    $timeAllocation[$ilo->code] = null;
                }
                return $timeAllocation;
            }
        );

        return $timeAllocation;
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
        $versions = $this->getVersions();
        if ($this->version != null && array_key_exists($this->version, $versions)) {
            return $versions[$this->version];
        } else {
            return "Unknown";
        }
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
}