<?php

namespace App\Domains\AcademicProgram;

use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicProgram extends Model
{
    use HasFactory;

    const CACHE_DURATION = 3600; // Cache duration in seconds (1 hour)

    public static function getAcademicPrograms(): array
    {
        return [
            'undergraduate' => 'Undergraduate',
            'postgraduate' => 'Postgraduate'
        ];
    }

    /*
    * Get the versions of the academic programs.
    * This is used to get the versions of the academic programs
    *
    * @param string|null $academicProgram
    * @return array
    */
    public static function getVersions($academicProgram = null): array
    {
        $cacheKey = 'academic_program_versions';
        $academicPrograms = cache()->remember(
            $cacheKey,
            now()->addSeconds(self::CACHE_DURATION),
            function () {
                $undergraduate = TaxonomyTerm::where('code', 'academic_program_undergraduate')->firstOrFail();
                $postgraduate = TaxonomyTerm::where('code', 'academic_program_postgraduate')->firstOrFail();

                $academicPrograms = [
                    'undergraduate' => [],
                    'postgraduate' => []
                ];

                foreach (['undergraduate' => $undergraduate, 'postgraduate' => $postgraduate] as $type => $program) {
                    foreach ($program->children as $child) {
                        $code = (int) $child->getFormattedMetadata('key');
                        $academicPrograms[$type][$code] = $child->name;
                    }
                }

                return $academicPrograms;
            }
        );

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

    /*
    * Get the course types for the academic program.
    * This is used to get the course types such as Foundation, Core, GE, etc.
    *
    * @return array
    */
    public static function getTypes(): array
    {
        $cacheKey = 'academic_program_course_types';
        $types = cache()->remember(
            $cacheKey,
            now()->addSeconds(self::CACHE_DURATION),
            function () {
                $courseTypes = TaxonomyTerm::where('code', 'course_types')->firstOrFail();
                $types = [];
                foreach ($courseTypes->children as $child) {
                    $types[$child->getFormattedMetadata('key')] = $child->name;
                }
                return $types;
            }
        );

        return $types;
    }
}