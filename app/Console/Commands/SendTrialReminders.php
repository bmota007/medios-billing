<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionInviteMail;
use Carbon\Carbon;

class SendTrialReminders extends Command
{
    protected $signature = 'billing:reminders';
    protected $description = 'Send automated trial expiration reminders';

    public function handle()
    {
        // Find companies in trial
        $companies = Company::where('subscription_status', 'trial')->get();

        foreach ($companies as $company) {
            if (!$company->trial_ends_at) continue;

            $daysLeft = Carbon::now('America/Chicago')->diffInDays($company->trial_ends_at, false);
            
            // Logic: Send on Day 5, Day 2, and Day 0 (Expired)
            if (in_array($daysLeft, [5, 2, 0])) {
                // Find the admin of that specific company
                $owner = User::where('company_id', $company->id)
                             ->where('role', 'admin')
                             ->first();

                if ($owner) {
                    Mail::to($owner->email)->send(new SubscriptionInviteMail($company, $daysLeft));
                    $this->info("Reminder sent to {$company->name} ({$daysLeft} days left)");
                }
            }
        }
    }
}
