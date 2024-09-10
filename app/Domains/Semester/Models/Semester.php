<?php

namespace App\Domains\Semester\Models;

use App\Domains\Semester\Models\Traits\Scope\SemesterScope;
use Database\Factories\SemesterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class Semester extends Model
{
    use SemesterScope,
        HasFactory,
        LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;


    protected $guarded = ['id'];


    /**
     * @var string[]
     */
    protected $casts = [
        'title' => 'string',
        'version' => 'integer',
        'academic_program' => 'string',
        'description' => 'string',
        'url' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];
    public static function getAcademicPrograms(): array
    {
        return [
            'Undergraduate' => 'Undergraduate',
            'Postgraduate' => 'Postgraduate'
        ];
    }

    public static function getVersions(): array
    {
        return [
            1 => 'Current Curriculum',
            2 => 'Curriculum - Effective from E22'
        ];
    }



    // Accessor to check if this is the latest syllabus version
    public function getIsNewSyllabusAttribute()
    {
        $maxVersion = self::where('title', $this->title)->max('version');
        return $this->version === $maxVersion;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SemesterFactory::new();
    }
}