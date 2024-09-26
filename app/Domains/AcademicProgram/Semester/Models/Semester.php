<?php

namespace App\Domains\AcademicProgram\Semester\Models;

use App\Domains\Auth\Models\User;
use App\Domains\AcademicProgram\AcademicProgram;
use App\Domains\AcademicProgram\Semester\Models\Traits\Scope\SemesterScope;
use Database\Factories\SemesterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class Semester extends AcademicProgram
{
    use SemesterScope,
        HasFactory,
        LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    protected $guarded = ['id'];

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


    public function getLatestSyllabusAttribute()
    {
        $maxVersion = self::where('title', $this->title)->max('version');
        return $this->version === $maxVersion;
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function academicProgram()
    {
        return $this->getAcademicPrograms()[$this->academic_program];
    }

    protected static function newFactory()
    {
        return SemesterFactory::new();
    }
}