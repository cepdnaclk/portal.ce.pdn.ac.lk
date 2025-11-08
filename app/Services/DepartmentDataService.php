<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DepartmentDataService
{
  public function isInternalEmail($userEmail)
  {
    $emails = Cache::remember(
      'dept_service_user_emails',
      config('constants.department_data.cache_duration'),
      function () {

        // Students
        $students = $this->getData('/people/v1/students/all/');
        $student_emails = collect($students)->map(function ($user) {
          $faculty_name = $user['emails']['faculty']['name'];
          $faculty_domain = $user['emails']['faculty']['domain'];

          $personal_name = $user['emails']['faculty']['name'];
          $personal_domain = $user['emails']['faculty']['domain'];

          if ($faculty_domain == 'eng.pdn.ac.lk' && $faculty_name != '' && $faculty_domain != '') {
            // Faculty Email
            return "$faculty_name@$faculty_domain";
          } elseif ($personal_domain == 'eng.pdn.ac.lk') {
            // Personal Email
            return "$personal_name@$personal_domain";
          }
          return null;
        });

        // Staff
        $staff = $this->getData('/people/v1/staff/all/');
        $staff_emails = collect($staff)->map(function ($user) {
          return $user['email'];
        });

        return $student_emails->union($staff_emails)->filter()->values()->toArray();
      }
    );
    return in_array($userEmail, $emails);
  }


  public function getProjectData($url)
  {
    $project = Cache::remember(
      "project_$url",
      config('constants.department_data.cache_duration'),
      function () use ($url) {
        return  $this->getData($url);
      }
    );
    return $project;
  }

  public function getRolesByDepartmentEmail(string $email): ?array
  {
    // Check staff first
    $staff = Cache::remember(
      'dept_service_staff',
      config('constants.department_data.cache_duration'),
      function () {
        return $this->getData('/people/v1/staff/all/');
      }
    );
    $staffMember = collect($staff)->firstWhere('email', $email);

    if ($staffMember) {
      $staffMap = [
        'Lecturer' => ['Lecturer'],
        'Senior Lecturer' => ['Lecturer'],
        'Professor' => ['Lecturer'],
        'Temporary Academic Staff' => ['Temporary Academic Staff'],
        'Temporary Lecturer' => ['Temporary Academic Staff'],
        'Visiting Lecturer' => ['Temporary Academic Staff'],
        'Academic Support Staff' => ['Academic Support Staff'],
        'Technical Officer' => ['Academic Support Staff'],
        'Senior Technical Officer' => ['Academic Support Staff'],
      ];
      return $staffMap[$staffMember['designation']] ?? null;
    }

    // Check students
    $students = Cache::remember(
      'dept_service_students',
      config('constants.department_data.cache_duration'),
      function () {
        return $this->getData('/people/v1/students/all/');
      }
    );

    $student = collect($students)->first(function ($student) use ($email) {
      // Construct faculty email
      $faculty_name = $student['emails']['faculty']['name'] ?? '';
      $faculty_domain = $student['emails']['faculty']['domain'] ?? '';
      $facultyEmail = ($faculty_name && $faculty_domain) ? "$faculty_name@$faculty_domain" : null;

      // Construct personal email
      $personal_name = $student['emails']['personal']['name'] ?? '';
      $personal_domain = $student['emails']['personal']['domain'] ?? '';
      $personalEmail = ($personal_name && $personal_domain) ? "$personal_name@$personal_domain" : null;

      return $facultyEmail === $email || $personalEmail === $email;
    });

    if ($student) {
      return ['Student'];
    }

    return null;
  }

  private function getData($endpoint)
  {
    $url = config('constants.department_data.base_url') . $endpoint;

    try {
      $response = Http::get($url);
    } catch (\Exception $e) {
      Log::error('Error in getData: ' . $e->getMessage());
      return [];
    }

    if ($response->successful()) {
      return $response->json();
    }

    Log::error('Error in getData: ' . $response->body());
    return [];
  }
}