<?php

namespace App\Domains\Semester\Models;

use App\Domains\Auth\Models\User;
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
            'undergraduate' => 'Undergraduate',
            'postgraduate' => 'Postgraduate'
        ];
    }

    public static function getVersions(): array
    {
        // TODO integrate with Taxonomies 
        return [
            1 => 'Current Curriculum',
            2 => 'Curriculum - Effective from E22'
        ];
    }

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