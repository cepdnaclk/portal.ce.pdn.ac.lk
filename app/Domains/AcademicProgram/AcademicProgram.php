<?php

namespace App\Domains\AcademicProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        } else if (array_key_exists($academicProgram, $academicPrograms)) {
            return $academicPrograms[$academicProgram];
        } else {
            return [];
        }
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
}