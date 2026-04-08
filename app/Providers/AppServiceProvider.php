<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

            $brandName = 'Medios Billing';
            $brandLogo = null;
            $brandColor = '#6366f1';

            if (Auth::check()) {
                $user = Auth::user();

                // SAFETY CHECK: Ensure company exists before accessing properties
                if ($user && isset($user->company)) {
                    $brandName = $user->company->name ?? 'Medios Billing';
                    $brandLogo = $user->company->logo ?? null;
                    $brandColor = $user->company->primary_color ?? '#6366f1';
                }
            }

            $view->with([
                'brandName' => $brandName,
                'brandLogo' => $brandLogo,
                'brandColor' => $brandColor
            ]);
        });
    }
}
