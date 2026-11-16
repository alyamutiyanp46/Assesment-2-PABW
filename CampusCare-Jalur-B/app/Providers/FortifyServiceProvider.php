<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(fn () => Inertia::render('auth/login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'status' => session('status'),
        ]));
        Fortify::registerView(fn () => Inertia::render('auth/register'));
        Fortify::requestPasswordResetLinkView(fn () => Inertia::render('auth/forgot-password', [
            'status' => session('status'),
        ]));
        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/reset-password', [
            'token' => $request->route('token'),
            'email' => $request->email,
        ]));
        Fortify::verifyEmailView(fn () => Inertia::render('auth/verify-email', [
            'status' => session('status'),
        ]));
        Fortify::confirmPasswordView(fn () => Inertia::render('auth/confirm-password'));

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

    }
}
