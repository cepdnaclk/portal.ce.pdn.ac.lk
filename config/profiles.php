<?php

return [
  'required_fields' => [
    'full_name',
    'email',
    'type',
  ],

  'sync' => [
    'students_url' => 'https://api.ce.pdn.ac.lk/people/v1/students/all/',
    'staff_url' => 'https://api.ce.pdn.ac.lk/people/v1/staff/all/',
    'timeout' => 30,
  ],

  'shared_identity_fields' => [
    'full_name',
    'name_with_initials',
    'preferred_short_name',
    'preferred_long_name',
    'gender',
    'profile_website',
    'profile_linkedin',
    'profile_github',
    'profile_researchgate',
    'profile_google_scholar',
    'profile_orcid',
    'profile_facebook',
    'profile_twitter',
  ],
  'department' => ['Department of Computer Engineering', 'Department of Mechanical Engineering'],
  'image' => [
    'disk' => 'public',
    'directory' => 'profiles',
    'max_size_kb' => 2048,
    'allowed_mimes' => ['image/jpeg'],
    'extensions' => ['jpg', 'jpeg'],
    'min_ratio' => 0.75,
    'max_ratio' => 1.0,
  ],
];
