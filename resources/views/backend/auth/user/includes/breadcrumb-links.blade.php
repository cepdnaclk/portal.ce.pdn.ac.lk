<x-utils.link class="c-subheader-nav-link" :href="route('dashboard.auth.user.deactivated')" :text="__('Deactivated Users')"
    permission="admin.access.user.reactivate" />

@if ($logged_in_user->hasAllAccess())
    <x-utils.link class="c-subheader-nav-link" :href="route('dashboard.auth.user.deleted')" :text="__('Deleted Users')" />
@endif
