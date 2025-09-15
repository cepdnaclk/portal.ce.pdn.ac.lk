<?php

namespace App\Domains\Auth\Listeners;

use App\Domains\Auth\Events\User\UserCreated;
use App\Services\DepartmentDataService;

class AssignDepartmentRole
{
    protected DepartmentDataService $departmentDataService;

    public function __construct(DepartmentDataService $departmentDataService)
    {
        $this->departmentDataService = $departmentDataService;
    }

    public function handle(UserCreated $event): void
    {
        $user = $event->user;
        $role = $this->departmentDataService->getRoleForEmail($user->email);

        if ($role) {
            $user->assignRole($role);
        }
    }
}
