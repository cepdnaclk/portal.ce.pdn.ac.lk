<div>
    <x-utils.view-button :href="route('dashboard.tenants.users', $model)" icon="far fa-user" />
    <x-utils.edit-button :href="route('dashboard.tenants.edit', $model)" />

    @if ($assignmentsCount === 0)
        <x-utils.delete-button :href="route('dashboard.tenants.destroy', $model)" :text="__('')" />
    @endif
</div>
