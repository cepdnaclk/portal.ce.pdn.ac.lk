<?php

namespace App\Domains\AcademicProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class AcademicProgram extends Model
{
    use HasFactory;

    public static function getAcademicPrograms(): array
    {
        return [
            'undergraduate' => 'Undergraduate',
            'postgraduate' => 'Postgraduate'
        ];
    }

    public static function getVersions($academicProgram = null): array
    {
        // TODO integrate with Taxonomies
        $academicPrograms = [
            'undergraduate' => [
                1 => 'Current Curriculum',
                2 => 'Curriculum - Effective from E22'
            ],
            'postgraduate' => [
                3 => 'Current Curriculum - PG',
            ]
        ];

        if ($academicProgram == null) {
            $allAcademicPrograms = [];
            foreach ($academicPrograms as $programs) {
                foreach ($programs as $key => $value) $allAcademicPrograms[$key] = $value;
            }
            return $allAcademicPrograms;
        } else if (array_key_exists(strtolower($academicProgram), $academicPrograms)) {
            return $academicPrograms[strtolower($academicProgram)];
        } else {
            return [];
        }
    }

    public function curriculum()
    {
        return $this::getVersions($this->academic_program)[$this->version] ?? null;
    }

    public static function getTypes(): array
    {
        return [
            'Foundation' => 'Foundation',
            'Core' => 'Core',
            'GE' => 'General Elective',
            'TE' => 'Technical Elective'
        ];
    }

    /**
     * Get the activity log options for the model.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['area', 'type', 'message', 'enabled', 'starts_at', 'ends_at'])
            ->logOnlyDirty();
    }
}