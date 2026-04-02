<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ContractTemplate;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SYSTEM OWNER
        $medios = Company::create([
            'name' => 'Medios Billing',
            'email' => 'admin@mediosbilling.com',
            'phone' => '8326388556',
            'is_active' => true,
            'plan' => 'SYSTEM', 
            'subscription_status' => 'active'
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mediosbilling.com',
            'password' => Hash::make('@Pegapalo22$'),
            'company_id' => $medios->id, 
            'role' => 'super_admin',
            'is_admin' => true,
        ]);

        // 2. CLIENT: MCS
        $mcs = Company::create([
            'name' => 'Mcintosh Cleaning Service',
            'email' => 'ventas@medioscorporativos.com',
            'phone' => '8327668585',
            'is_active' => true,
            'accept_card' => true,
            'accept_zelle' => true,
            'zelle_label' => 'Zelle',
            'zelle_value' => 'ventas@medioscorporativos.com',
            'trial_ends_at' => now()->addDays(30),
        ]);

        User::create([
            'name' => 'Patricia McIntosh',
            'email' => 'ventas@medioscorporativos.com',
            'password' => Hash::make('Mcs2026!'),
            'company_id' => $mcs->id,
            'role' => 'admin',
        ]);

        // 3. CLIENT: PRONTO
        $pronto = Company::create([
            'name' => 'Pronto Painting',
            'email' => 'info@prontopainting.net',
            'phone' => '513-807-6946',
            'street_address' => '5177 Leona Dr',
            'city_state_zip' => 'Cincinnati, OH 45238',
            'is_active' => true,
            'accept_card' => true,
            'accept_zelle' => true,
            'zelle_label' => 'Zelle',
            'zelle_value' => 'info@prontopainting.net',
            'trial_ends_at' => now()->addDays(30),
        ]);

        User::create([
            'name' => 'Esmery Lopez',
            'email' => 'info@prontopainting.net',
            'password' => Hash::make('Pronto2026!'),
            'company_id' => $pronto->id,
            'role' => 'admin',
        ]);

        // 4. CUSTOMER HECTOR
        $hector = Customer::create([
            'company_id' => $mcs->id,
            'name' => 'Host Hector Diaz',
            'email' => 'bmota007@gmail.com',
            'phone' => '83263888556',
            'billing_address' => 'Real Customer Address',
            'street_address' => 'Real Customer Address',
            'city' => 'Houston', 'state' => 'TX', 'zip' => '77001',
            'city_state_zip' => 'Houston, TX 77001'
        ]);

        // 5. CONTRACT TEMPLATE (Essential for Quote Signing)
        ContractTemplate::create([
            'company_id' => $mcs->id,
            'name' => 'Standard Cleaning Contract',
            'contract_body' => '<h2>Service Agreement</h2><p>This agreement outlines the terms of service provided by MCS.</p>',
        ]);

        $this->command->info('Full Database Seeded Successfully!');
    }
}
