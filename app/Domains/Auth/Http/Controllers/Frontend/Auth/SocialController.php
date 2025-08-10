<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use App\Domains\Auth\Events\User\UserLoggedIn;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Rules\ValidateAsInternalEmail;
use Illuminate\Support\Facades\Log;

/**
 * Class SocialController.
 */
class SocialController
{
    /**
     * @param $provider
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param $provider
     * @param  UserService  $userService
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function callback($provider, UserService $userService)
    {
        try {
            // Validate for internal user
            $info = Socialite::driver($provider)->user();
            $validator = Validator::make(
                ['email' => $info->email, 'name' => $info->name],
                ['email' => ['required', 'email', new ValidateAsInternalEmail()], 'name' => ['required']]
            );

            if ($validator->fails()) {
                $errorMessage = "";
                $errors = $validator->errors();

                foreach ($errors->messages() as $key => $messages) {
                    if (is_array($messages)) {
                        foreach ($messages as $message) {
                            $errorMessage .= $message . ' ';
                        }
                    } else {
                        $errorMessage .= $messages . ' ';
                    }
                }
                return redirect()->route('frontend.auth.login')->withFlashDanger(trim($errorMessage));
            }


            // Check if a user with the same email already exists
            $existingUser = User::where('email', $info->getEmail())->first();

            if (!$existingUser) {
                $user = $userService->registerProvider($info, $provider);
            } else {
                $user = $existingUser;
                // // If the user exists but is not linked to the provider, link it
                // if (!$user->provider) {
                //     $userService->linkProvider($user, $info, $provider);
                // }
            }

            if (!$user->isActive()) {
                auth()->logout();
                return redirect()->route('frontend.auth.login')->withFlashDanger(__('Your account has been deactivated.'));
            }

            auth()->login($user);

            event(new UserLoggedIn($user));

            return redirect()->route(homeRoute());
        } catch (\Throwable $e) {
            \Log::error('Social callback failed', [
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
                'user_id'   => $info?->getId(),
                'user_email' => $info?->getEmail(),
            ]);
            return redirect()->route('frontend.auth.login')
                ->withErrors('Social login failed. Please try again.');
        }
    }
}