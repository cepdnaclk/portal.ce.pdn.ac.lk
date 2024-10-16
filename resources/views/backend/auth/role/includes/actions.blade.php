@if (!$model->isAdmin())
    <x-utils.edit-button :href="route('dashboard.auth.role.edit', $model)" />
    <x-utils.delete-button :href="route('dashboard.auth.role.destroy', $model)" />
@endif
