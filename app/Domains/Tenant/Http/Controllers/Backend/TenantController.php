<?php

namespace App\Domains\Tenant\Http\Controllers\Backend;

use App\Domains\Tenant\Models\Tenant;
use App\Domains\Tenant\Services\TenantService;
use Illuminate\Http\Request;

/**
 * Class TenantController.
 */
class TenantController
{
  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    return view('backend.tenant.index');
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function create()
  {
    return view('backend.tenant.create');
  }

  /**
   * @throws \App\Exceptions\GeneralException
   * @throws \Throwable
   */
  public function store(Request $request, TenantService $tenantService)
  {
    $tenantService->store($request->all());

    return redirect()->route('dashboard.tenants.index')->withFlashSuccess(__('The tenant was successfully created.'));
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function edit(Tenant $tenant)
  {
    return view('backend.tenant.edit')
      ->withTenant($tenant);
  }

  /**
   * @throws \App\Exceptions\GeneralException
   * @throws \Throwable
   */
  public function update(Request $request, Tenant $tenant, TenantService $tenantService)
  {
    $tenantService->update($tenant, $request->all());

    return redirect()->route('dashboard.tenants.index')->withFlashSuccess(__('The tenant was successfully updated.'));
  }

  /**
   * @throws \Exception
   */
  public function destroy(Tenant $tenant, TenantService $tenantService)
  {
    $tenantService->destroy($tenant);

    return redirect()->route('dashboard.tenants.index')->withFlashSuccess(__('The tenant was successfully deleted.'));
  }
}
