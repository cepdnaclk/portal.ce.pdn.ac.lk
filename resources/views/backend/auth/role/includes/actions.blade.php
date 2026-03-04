<x-utils.view-button :href="route('dashboard.auth.role.users', $model)" icon="far fa-user" />

@if (!$model->isAdmin())
    <x-utils.edit-button :href="route('dashboard.auth.role.edit', $model)" />
    <x-utils.delete-button :href="route('dashboard.auth.role.destroy', $model)" />
@endif
