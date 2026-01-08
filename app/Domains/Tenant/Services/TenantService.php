<?php

namespace App\Domains\Tenant\Services;

use App\Domains\Tenant\Models\Tenant;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenantService extends BaseService
{
  public function __construct(Tenant $tenant)
  {
    $this->model = $tenant;
  }

  /**
   * @param  array  $data
   * @return Tenant
   *
   * @throws GeneralException
   * @throws \Throwable
   */
  public function store(array $data = []): Tenant
  {
    $data = $this->validateStore($data);
    $data['is_default'] = (bool) ($data['is_default'] ?? false);

    DB::beginTransaction();

    try {
      $tenant = $this->model::create([
        'slug' => $data['slug'],
        'name' => $data['name'],
        'url' => $data['url'],
        'description' => $data['description'] ?? null,
        'is_default' => $data['is_default'],
      ]);

      if ($data['is_default']) {
        $this->model::query()
          ->where('id', '!=', $tenant->id)
          ->update(['is_default' => false]);
      }
    } catch (Exception $e) {
      DB::rollBack();

      throw new GeneralException(__('There was a problem creating the tenant.'));
    }

    DB::commit();

    return $tenant;
  }

  /**
   * @param  Tenant  $tenant
   * @param  array  $data
   * @return Tenant
   *
   * @throws GeneralException
   * @throws \Throwable
   */
  public function update(Tenant $tenant, array $data = []): Tenant
  {
    $data = $this->validateUpdate($tenant, $data);
    $data['is_default'] = (bool) ($data['is_default'] ?? false);

    DB::beginTransaction();

    try {
      $tenant->update([
        'slug' => $data['slug'],
        'name' => $data['name'],
        'url' => $data['url'],
        'description' => $data['description'] ?? null,
        'is_default' => $data['is_default'],
      ]);

      if ($data['is_default']) {
        $this->model::query()
          ->where('id', '!=', $tenant->id)
          ->update(['is_default' => false]);
      }
    } catch (Exception $e) {
      DB::rollBack();

      throw new GeneralException(__('There was a problem updating the tenant.'));
    }

    DB::commit();

    return $tenant;
  }

  /**
   * @param  Tenant  $tenant
   * @return bool
   *
   * @throws GeneralException
   */
  public function destroy(Tenant $tenant): bool
  {
    if ($this->hasAssignments($tenant)) {
      throw new GeneralException(__('You can not delete a tenant with associated resources.'));
    }

    if ($this->deleteById($tenant->id)) {
      return true;
    }

    throw new GeneralException(__('There was a problem deleting the tenant.'));
  }

  private function validateStore(array $data): array
  {
    return Validator::validate($data, [
      'slug' => ['required', 'max:255', Rule::unique('tenants', 'slug')],
      'name' => ['required', 'max:255'],
      'url' => ['required', 'max:255', 'url'],
      'description' => ['nullable', 'max:255'],
      'is_default' => ['sometimes', 'boolean'],
    ]);
  }

  private function validateUpdate(Tenant $tenant, array $data): array
  {
    return Validator::validate($data, [
      'slug' => ['required', 'max:255', Rule::unique('tenants', 'slug')->ignore($tenant)],
      'name' => ['required', 'max:255'],
      'url' => ['required', 'max:255', 'url'],
      'description' => ['nullable', 'max:255'],
      'is_default' => ['sometimes', 'boolean'],
    ]);
  }

  private function hasAssignments(Tenant $tenant): bool
  {
    return $tenant->users()->exists()
      || $tenant->roles()->exists()
      || $tenant->news()->exists()
      || $tenant->events()->exists()
      || $tenant->announcements()->exists();
  }
}
