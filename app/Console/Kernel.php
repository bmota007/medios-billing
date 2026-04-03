<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Company;
use App\Mail\TrialCheckInMail;
use App\Mail\WelcomeOnboardMail;
use App\Mail\PaymentFailedMail;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $companies = Company::all();

            foreach ($companies as $company) {
                $daysActive = now()->diffInDays($company->created_at);

                // --- TRIAL LOGIC ---
                
                // DAY 3: The "How is it going?" Email
                if ($daysActive == 3 && $company->subscription_status == 'trialing') {
                    Mail::to($company->email)->send(new TrialCheckInMail($company));
                }

                // DAY 7: The Big Charge
                if ($daysActive == 7 && $company->subscription_status == 'trialing') {
                    $this->processInitialCharge($company);
                }

                // --- DUNNING LOGIC (Payment Failure) ---

                if ($company->subscription_status == 'past_due') {
                    $failDays = now()->diffInDays($company->last_failed_payment_at);

                    // Daily reminder until Day 7
                    if ($failDays < 7) {
                        Mail::to($company->email)->send(new PaymentFailedMail($company, $failDays));
                    }

                    // DAY 3: LOCK ACCOUNT
                    if ($failDays == 3) {
                        $company->update(['is_active' => false]);
                    }

                    // DAY 7: PURGE / DELETE
                    if ($failDays == 7) {
                        $company->delete(); // Soft delete recommended
                    }
                }
            }
        })->dailyAt('08:00');
    }

    protected function processInitialCharge($company) {
        try {
            // Stripe Charge Logic here using $company->stripe_payment_method_id
            // If success:
            $company->update(['subscription_status' => 'active', 'is_active' => true]);
            Mail::to($company->email)->send(new WelcomeOnboardMail($company));
        } catch (\Exception $e) {
            $company->update(['subscription_status' => 'past_due', 'last_failed_payment_at' => now()]);
        }
    }
}
