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

    public static function getVersions(): array
    {
        // TODO integrate with Taxonomies 
        return [
            1 => 'Current Curriculum',
            2 => 'Curriculum - Effective from E22'
        ];
    }

    public static function getTypes(): array
    {
        return [
            'Found' => 'Foundation',
            'Core' => 'Core',
            'GE' => 'General Elective',
            'TE' => 'Technical Elective'
        ];
    }
}