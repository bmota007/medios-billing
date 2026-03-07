<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (Auth::check()) {

        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return redirect()->route('login');

});


/*
|--------------------------------------------------------------------------
| PUBLIC PAYMENT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/payments', [InvoiceController::class,'paymentPage'])
    ->name('invoice.payment.page');

Route::post('/payments/checkout', [InvoiceController::class,'checkout'])
    ->name('invoice.checkout');

Route::get('/payments/success', [InvoiceController::class,'success'])
    ->name('invoice.payment.success');

Route::post('/stripe/webhook', [InvoiceController::class,'stripeWebhook'])
    ->name('stripe.webhook');


/*
|--------------------------------------------------------------------------
| AUTH PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Shared SaaS Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard',[DashboardController::class,'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | COMPANY SETTINGS
    |--------------------------------------------------------------------------
    */

    Route::get('/settings/company',[CompanyController::class,'settings'])
        ->name('company.settings');

    Route::post('/settings/company',[CompanyController::class,'update'])
        ->name('company.update');


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get('/customers',[InvoiceController::class,'customersIndex'])
        ->name('customers.index');

    Route::get('/customers/create',[InvoiceController::class,'customersCreate'])
        ->name('customers.create');

    Route::post('/customers/store',[InvoiceController::class,'customersStore'])
        ->name('customers.store');

    Route::get('/customers/{customer}/edit',[InvoiceController::class,'customersEdit'])
        ->name('customers.edit');

    Route::put('/customers/{customer}',[InvoiceController::class,'customersUpdate'])
        ->name('customers.update');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile',[ProfileController::class,'edit'])
        ->name('profile.edit');

    Route::patch('/profile',[ProfileController::class,'update'])
        ->name('profile.update');

    Route::delete('/profile',[ProfileController::class,'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | INVOICE SYSTEM
    |--------------------------------------------------------------------------
    */

    Route::match(['get','post'],'/invoice',[InvoiceController::class,'showForm'])
        ->name('invoice.form');

    Route::get('/invoice/create',[InvoiceController::class,'createInvoice'])
        ->name('invoice.create');

    Route::post('/invoice/preview',[InvoiceController::class,'preview'])
        ->name('invoice.preview');

    Route::post('/invoice/send',[InvoiceController::class,'send'])
        ->name('invoice.send');


    /*
    |--------------------------------------------------------------------------
    | INVOICE HISTORY
    |--------------------------------------------------------------------------
    */

    Route::get('/invoices',[InvoiceController::class,'history'])
        ->name('invoice.history');

    Route::get('/invoice/{invoice}',[InvoiceController::class,'view'])
        ->name('invoice.view');

    Route::get('/invoice/{invoice}/receipt',[InvoiceController::class,'receipt'])
        ->name('invoice.receipt');

    Route::post('/invoice/{invoice}/resend',[InvoiceController::class,'resend'])
        ->name('invoice.resend');

    Route::post('/invoice/{invoice}/resend-receipt',[InvoiceController::class,'resendReceipt'])
        ->name('invoice.resendReceipt');

    Route::post('/invoice/{invoice}/mark-paid',[InvoiceController::class,'markPaid'])
        ->name('invoice.markPaid');

    Route::delete('/invoice/{invoice}',[InvoiceController::class,'destroy'])
        ->name('invoice.destroy');

    Route::get('/invoices/export/csv',[InvoiceController::class,'exportCsv'])
        ->name('invoice.export.csv');

});


/*
|--------------------------------------------------------------------------
| SUPER ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','superadmin'])->group(function () {

    Route::get('/admin',[AdminController::class,'dashboard'])
        ->name('admin.dashboard');

    Route::get('/admin/companies',[AdminController::class,'companies'])
        ->name('admin.companies');

    Route::get('/admin/company/{id}',[AdminController::class,'company']);

});


require __DIR__.'/auth.php';
