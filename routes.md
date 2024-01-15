+--------+----------------------------------------+------------------------------------------------------+--------------------------------------------+--------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
|[32m Domain [39m|[32m Method [39m|[32m URI [39m|[32m Name [39m|[32m Action [39m|[32m Middleware [39m|
+--------+----------------------------------------+------------------------------------------------------+--------------------------------------------+--------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| | GET|HEAD | / | frontend.index | App\Http\Controllers\Frontend\HomeController@index | web |
| | | | | | breadcrumbs |
| | GET|HEAD | 2fa/confirm | 2fa.confirm | DarkGhostHunter\Laraguard\Http\Controllers\Confirm2FACodeController@showConfirmForm | web |
| | | | | | App\Http\Middleware\Authenticate |
| | POST | 2fa/confirm | | DarkGhostHunter\Laraguard\Http\Controllers\Confirm2FACodeController@confirm | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | Illuminate\Routing\Middleware\ThrottleRequests:60,1 |
| | GET|HEAD | \_debugbar/assets/javascript | debugbar.assets.js | Barryvdh\Debugbar\Controllers\AssetController@js | Barryvdh\Debugbar\Middleware\DebugbarEnabled |
| | | | | | Closure |
| | GET|HEAD | \_debugbar/assets/stylesheets | debugbar.assets.css | Barryvdh\Debugbar\Controllers\AssetController@css | Barryvdh\Debugbar\Middleware\DebugbarEnabled |
| | | | | | Closure |
| | DELETE | \_debugbar/cache/{key}/{tags?} | debugbar.cache.delete | Barryvdh\Debugbar\Controllers\CacheController@delete | Barryvdh\Debugbar\Middleware\DebugbarEnabled |
| | | | | | Closure |
| | GET|HEAD | \_debugbar/clockwork/{id} | debugbar.clockwork | Barryvdh\Debugbar\Controllers\OpenHandlerController@clockwork | Barryvdh\Debugbar\Middleware\DebugbarEnabled |
| | | | | | Closure |
| | GET|HEAD | \_debugbar/open | debugbar.openhandler | Barryvdh\Debugbar\Controllers\OpenHandlerController@handle | Barryvdh\Debugbar\Middleware\DebugbarEnabled |
| | | | | | Closure |
| | GET|HEAD | account | frontend.user.account | App\Http\Controllers\Frontend\User\AccountController@index | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | breadcrumbs |
| | DELETE | account/2fa | frontend.auth.account.2fa.destroy | App\Domains\Auth\Http\Controllers\Frontend\Auth\DisableTwoFactorAuthenticationController@destroy | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus:enabled |
| | GET|HEAD | account/2fa/disable | frontend.auth.account.2fa.disable | App\Domains\Auth\Http\Controllers\Frontend\Auth\DisableTwoFactorAuthenticationController@show | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus:enabled |
| | | | | | breadcrumbs |
| | GET|HEAD | account/2fa/enable | frontend.auth.account.2fa.create | App\Domains\Auth\Http\Controllers\Frontend\Auth\TwoFactorAuthenticationController@create | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus:disabled |
| | | | | | breadcrumbs |
| | GET|HEAD | account/2fa/recovery | frontend.auth.account.2fa.show | App\Domains\Auth\Http\Controllers\Frontend\Auth\TwoFactorAuthenticationController@show | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus:enabled |
| | | | | | breadcrumbs |
| | PATCH | account/2fa/recovery/generate | frontend.auth.account.2fa.update | App\Domains\Auth\Http\Controllers\Frontend\Auth\TwoFactorAuthenticationController@update | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus:enabled |
| | GET|HEAD | admin/log-viewer | log-viewer::dashboard | Arcanedev\LogViewer\Http\Controllers\LogViewerController@index | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | admin/log-viewer/logs | log-viewer::logs.list | Arcanedev\LogViewer\Http\Controllers\LogViewerController@listLogs | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | DELETE | admin/log-viewer/logs/delete | log-viewer::logs.delete | Arcanedev\LogViewer\Http\Controllers\LogViewerController@delete | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | admin/log-viewer/logs/{date} | log-viewer::logs.show | Arcanedev\LogViewer\Http\Controllers\LogViewerController@show | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | admin/log-viewer/logs/{date}/download | log-viewer::logs.download | Arcanedev\LogViewer\Http\Controllers\LogViewerController@download | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | admin/log-viewer/logs/{date}/{level} | log-viewer::logs.filter | Arcanedev\LogViewer\Http\Controllers\LogViewerController@showByLevel | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | admin/log-viewer/logs/{date}/{level}/search | log-viewer::logs.search | Arcanedev\LogViewer\Http\Controllers\LogViewerController@search | web |
| | | | | | admin |
| | | | | | App\Domains\Auth\Http\Middleware\SuperAdminCheck |
| | GET|HEAD | dashboard | frontend.user.intranet | App\Http\Controllers\Frontend\User\DashboardController@index | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | | | | | App\Domains\Auth\Http\Middleware\UserCheck |
| | | | | | breadcrumbs |
| | GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | dashboard | dashboard. | Illuminate\Routing\RedirectController | web |
| | | | | | admin |
| | GET|HEAD | dashboard/announcements | dashboard.announcements.index | Closure | web |
| | | | | | admin |
| | | | | | breadcrumbs |
| | POST | dashboard/announcements | dashboard.announcements.store | App\Http\Controllers\Backend\AnnouncementController@store | web |
| | | | | | admin |
| | GET|HEAD | dashboard/announcements/create | dashboard.announcements.create | App\Http\Controllers\Backend\AnnouncementController@create | web |
| | | | | | admin |
| | | | | | breadcrumbs |
| | GET|HEAD | dashboard/announcements/delete/{announcement} | dashboard.announcements.delete | App\Http\Controllers\Backend\AnnouncementController@delete | web |
| | | | | | admin |
| | | | | | breadcrumbs |
| | GET|HEAD | dashboard/announcements/edit/{announcement} | dashboard.announcements.edit | App\Http\Controllers\Backend\AnnouncementController@edit | web |
| | | | | | admin |
| | | | | | breadcrumbs |
| | PUT | dashboard/announcements/{announcement} | dashboard.announcements.update | App\Http\Controllers\Backend\AnnouncementController@update | web |
| | | | | | admin |
| | DELETE | dashboard/announcements/{announcement} | dashboard.announcements.destroy | App\Http\Controllers\Backend\AnnouncementController@destroy | web |
| | | | | | admin |
| | GET|HEAD | dashboard/auth/role | dashboard.auth.role.index | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@index | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | POST | dashboard/auth/role | dashboard.auth.role.store | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@store | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | GET|HEAD | dashboard/auth/role/create | dashboard.auth.role.create | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@create | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | PATCH | dashboard/auth/role/{role} | dashboard.auth.role.update | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | DELETE | dashboard/auth/role/{role} | dashboard.auth.role.destroy | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@destroy | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | GET|HEAD | dashboard/auth/role/{role}/edit | dashboard.auth.role.edit | App\Domains\Auth\Http\Controllers\Backend\Role\RoleController@edit | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | POST | dashboard/auth/user | dashboard.auth.user.store | App\Domains\Auth\Http\Controllers\Backend\User\UserController@store | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | GET|HEAD | dashboard/auth/user | dashboard.auth.user.index | App\Domains\Auth\Http\Controllers\Backend\User\UserController@index | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | breadcrumbs |
| | GET|HEAD | dashboard/auth/user/create | dashboard.auth.user.create | App\Domains\Auth\Http\Controllers\Backend\User\UserController@create | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | GET|HEAD | dashboard/auth/user/deactivated | dashboard.auth.user.deactivated | App\Domains\Auth\Http\Controllers\Backend\User\DeactivatedUserController@index | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.reactivate |
| | | | | | breadcrumbs |
| | GET|HEAD | dashboard/auth/user/deleted | dashboard.auth.user.deleted | App\Domains\Auth\Http\Controllers\Backend\User\DeletedUserController@index | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | DELETE | dashboard/auth/user/{deletedUser}/permanently-delete | dashboard.auth.user.permanently-delete | App\Domains\Auth\Http\Controllers\Backend\User\DeletedUserController@destroy | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | PATCH | dashboard/auth/user/{deletedUser}/restore | dashboard.auth.user.restore | App\Domains\Auth\Http\Controllers\Backend\User\DeletedUserController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | PATCH | dashboard/auth/user/{user} | dashboard.auth.user.update | App\Domains\Auth\Http\Controllers\Backend\User\UserController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | DELETE | dashboard/auth/user/{user} | dashboard.auth.user.destroy | App\Domains\Auth\Http\Controllers\Backend\User\UserController@destroy | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | GET|HEAD | dashboard/auth/user/{user} | dashboard.auth.user.show | App\Domains\Auth\Http\Controllers\Backend\User\UserController@show | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list |
| | | | | | breadcrumbs |
| | POST | dashboard/auth/user/{user}/clear-session | dashboard.auth.user.clear-session | App\Domains\Auth\Http\Controllers\Backend\User\UserSessionController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.clear-session |
| | GET|HEAD | dashboard/auth/user/{user}/edit | dashboard.auth.user.edit | App\Domains\Auth\Http\Controllers\Backend\User\UserController@edit | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\RoleMiddleware:Administrator |
| | | | | | breadcrumbs |
| | PATCH | dashboard/auth/user/{user}/mark/{status} | dashboard.auth.user.mark | App\Domains\Auth\Http\Controllers\Backend\User\DeactivatedUserController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.deactivate|admin.access.user.reactivate |
| | GET|HEAD | dashboard/auth/user/{user}/password/change | dashboard.auth.user.change-password | App\Domains\Auth\Http\Controllers\Backend\User\UserPasswordController@edit | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.change-password |
| | | | | | breadcrumbs |
| | PATCH | dashboard/auth/user/{user}/password/change | dashboard.auth.user.change-password.update | App\Domains\Auth\Http\Controllers\Backend\User\UserPasswordController@update | web |
| | | | | | admin |
| | | | | | Illuminate\Auth\Middleware\RequirePassword:frontend.auth.password.confirm |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.list|admin.access.user.deactivate|admin.access.user.reactivate|admin.access.user.clear-session|admin.access.user.impersonate|admin.access.user.change-password |
| | | | | | Spatie\Permission\Middlewares\PermissionMiddleware:admin.access.user.change-password |
| | POST | email/resend | frontend.auth.verification.resend | App\Domains\Auth\Http\Controllers\Frontend\Auth\VerificationController@resend | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Routing\Middleware\ThrottleRequests:6,1 |
| | GET|HEAD | email/verify | frontend.auth.verification.notice | App\Domains\Auth\Http\Controllers\Frontend\Auth\VerificationController@show | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | GET|HEAD | email/verify/{id}/{hash} | frontend.auth.verification.verify | App\Domains\Auth\Http\Controllers\Frontend\Auth\VerificationController@verify | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Routing\Middleware\ValidateSignature |
| | | | | | Illuminate\Routing\Middleware\ThrottleRequests:6,1 |
| | GET|HEAD | impersonate/leave | impersonate.leave | Lab404\Impersonate\Controllers\ImpersonateController@leave | web |
| | GET|HEAD | impersonate/take/{id}/{guardName?} | impersonate | Lab404\Impersonate\Controllers\ImpersonateController@take | web |
| | | | | | App\Http\Middleware\Authenticate:web |
| | GET|HEAD | lang/{lang} | locale.change | App\Http\Controllers\LocaleController@change | web |
| | GET|HEAD | livewire/livewire.js | | Livewire\Controllers\LivewireJavaScriptAssets@source | |
| | GET|HEAD | livewire/livewire.js.map | | Livewire\Controllers\LivewireJavaScriptAssets@maps | |
| | POST | livewire/message/{name} | livewire.message | Livewire\Controllers\HttpConnectionHandler | web |
| | GET|HEAD | livewire/preview-file/{filename} | livewire.preview-file | Livewire\Controllers\FilePreviewHandler@handle | web |
| | POST | livewire/upload-file | livewire.upload-file | Livewire\Controllers\FileUploadHandler@handle | web |
| | | | | | Illuminate\Routing\Middleware\ThrottleRequests:60,1 |
| | GET|HEAD | login | frontend.auth.login | App\Domains\Auth\Http\Controllers\Frontend\Auth\LoginController@showLoginForm | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | POST | login | frontend.auth. | App\Domains\Auth\Http\Controllers\Frontend\Auth\LoginController@login | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | GET|HEAD | login/{provider} | frontend.auth.social.login | App\Domains\Auth\Http\Controllers\Frontend\Auth\SocialController@redirect | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | GET|HEAD | login/{provider}/callback | frontend.auth. | App\Domains\Auth\Http\Controllers\Frontend\Auth\SocialController@callback | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | POST | logout | frontend.auth.logout | App\Domains\Auth\Http\Controllers\Frontend\Auth\LoginController@logout | web |
| | | | | | App\Http\Middleware\Authenticate |
| | GET|HEAD | password/confirm | frontend.auth.password.confirm | App\Domains\Auth\Http\Controllers\Frontend\Auth\ConfirmPasswordController@showConfirmForm | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | POST | password/confirm | frontend.auth. | App\Domains\Auth\Http\Controllers\Frontend\Auth\ConfirmPasswordController@confirm | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | POST | password/email | frontend.auth.password.email | App\Domains\Auth\Http\Controllers\Frontend\Auth\ForgotPasswordController@sendResetLinkEmail | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | GET|HEAD | password/expired | frontend.auth.password.expired | App\Domains\Auth\Http\Controllers\Frontend\Auth\PasswordExpiredController@expired | web |
| | | | | | App\Http\Middleware\Authenticate |
| | PATCH | password/expired | frontend.auth.password.expired.update | App\Domains\Auth\Http\Controllers\Frontend\Auth\PasswordExpiredController@update | web |
| | | | | | App\Http\Middleware\Authenticate |
| | GET|HEAD | password/reset | frontend.auth.password.request | App\Domains\Auth\Http\Controllers\Frontend\Auth\ForgotPasswordController@showLinkRequestForm | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | POST | password/reset | frontend.auth.password.update | App\Domains\Auth\Http\Controllers\Frontend\Auth\ResetPasswordController@reset | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | GET|HEAD | password/reset/{token} | frontend.auth.password.reset | App\Domains\Auth\Http\Controllers\Frontend\Auth\ResetPasswordController@showResetForm | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | PATCH | password/update | frontend.auth.password.change | App\Domains\Auth\Http\Controllers\Frontend\Auth\UpdatePasswordController@update | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | PATCH | profile/update | frontend.user.profile.update | App\Http\Controllers\Frontend\User\ProfileController@update | web |
| | | | | | App\Http\Middleware\Authenticate |
| | | | | | App\Domains\Auth\Http\Middleware\PasswordExpires |
| | | | | | Illuminate\Auth\Middleware\EnsureEmailIsVerified:frontend.auth.verification.notice |
| | GET|HEAD | register | frontend.auth.register | App\Domains\Auth\Http\Controllers\Frontend\Auth\RegisterController@showRegistrationForm | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | POST | register | frontend.auth. | App\Domains\Auth\Http\Controllers\Frontend\Auth\RegisterController@register | web |
| | | | | | App\Http\Middleware\RedirectIfAuthenticated |
| | GET|HEAD | sanctum/csrf-cookie | | Laravel\Sanctum\Http\Controllers\CsrfCookieController@show | web |
| | GET|HEAD | terms | frontend.pages.terms | App\Http\Controllers\Frontend\TermsController@index | web |
| | | | | | breadcrumbs |
| | POST | {locale}/livewire/message/{name} | livewire.message-localized | Livewire\Controllers\HttpConnectionHandler | web |
+--------+----------------------------------------+------------------------------------------------------+--------------------------------------------+--------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
