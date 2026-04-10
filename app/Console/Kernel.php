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
        /**
         * 🔥 AUTO CHARGE REMAINING BALANCES
         */
        $schedule->call(function () {
            app(\App\Http\Controllers\InvoiceController::class)->autoChargeRemaining();
        })->daily()->withoutOverlapping();

        /**
         * 🔥 TRIAL + DUNNING SYSTEM
         */
        $schedule->call(function () {

            $companies = Company::all();

            foreach ($companies as $company) {

                $daysActive = now()->diffInDays($company->created_at);

                // DAY 3 → Check-in email
                if ($daysActive == 3 && $company->subscription_status == 'trialing') {
                    Mail::to($company->email)->send(new TrialCheckInMail($company));
                }

                // DAY 7 → Charge customer
                if ($daysActive == 7 && $company->subscription_status == 'trialing') {
                    try {
                        // ⚡ Stripe charge logic here later
                        $company->update([
                            'subscription_status' => 'active',
                            'is_active' => true
                        ]);

                        Mail::to($company->email)->send(new WelcomeOnboardMail($company));

                    } catch (\Exception $e) {
                        $company->update([
                            'subscription_status' => 'past_due',
                            'last_failed_payment_at' => now()
                        ]);
                    }
                }

                /**
                 * 🔥 FAILED PAYMENT RECOVERY
                 */
                if ($company->subscription_status == 'past_due') {

                    $failDays = now()->diffInDays($company->last_failed_payment_at);

                    if ($failDays < 7) {
                        Mail::to($company->email)->send(new PaymentFailedMail($company, $failDays));
                    }

                    if ($failDays == 3) {
                        $company->update(['is_active' => false]);
                    }

                    if ($failDays == 7) {
                        $company->delete(); // or soft delete
                    }
                }
            }

        })->dailyAt('08:00')->withoutOverlapping();

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
