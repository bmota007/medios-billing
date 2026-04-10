<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
// ❌ FIXED: This controller does NOT exist → COMMENTED
// use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SmsWebhookController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return auth()->user()->role === 'super_admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ❌ FIXED: These routes were breaking Laravel → COMMENTED
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| PUBLIC SMS WEBHOOK ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/sms/webhook', [SmsWebhookController::class, 'handle']);

/*
|--------------------------------------------------------------------------
| STRIPE WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| PUBLIC INVOICE & PAYMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/invoice/view/{invoice_no}', [InvoiceController::class, 'publicView'])->name('invoice.public_view');

Route::get('/invoice/pay/{invoice_no}', [InvoiceController::class, 'showPaymentPage'])->name('invoice.pay');

// ✅ FIXED HERE (post ➜ get)
Route::get('/invoice/checkout/{invoice_no}', [InvoiceController::class, 'stripeCheckout'])->name('invoice.checkout');

Route::get('/invoice/success/{invoice_no}', [InvoiceController::class, 'stripeSuccess'])->name('stripe.success');

Route::post('/invoice/{invoice_no}/manual-payment', [InvoiceController::class, 'submitManualPayment'])->name('invoice.manual.payment');

Route::get('/invoice/{invoice_no}/success', [InvoiceController::class, 'paymentSuccess'])->name('invoice.payment.success');

Route::put('/invoice/update/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');

/*
|--------------------------------------------------------------------------
| PUBLIC QUOTES
|--------------------------------------------------------------------------
*/
Route::get('/q/{token}', [QuoteController::class, 'publicView'])->name('quotes.public');
Route::post('/q/{token}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
Route::get('/q/{token}/contract', [QuoteController::class, 'showContract'])->name('quotes.contract');
Route::post('/q/{token}/contract/sign', [QuoteController::class, 'signContract'])->name('quotes.contract.sign');
Route::get('/quote/{id}/contract-view', [QuoteController::class, 'viewContractFile'])->name('quotes.contract.view');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| BILLING (NOT LOCKED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/subscribe', function () {
        return view('billing.subscribe');
    })->name('subscribe');

    Route::post('/process-subscription', [BillingController::class, 'processSubscription'])->name('process.subscription');
    Route::post('/billing/pay', [BillingController::class, 'processSubscription'])->name('billing.pay');
});

Route::get('/billing/expired', [BillingController::class, 'expired'])->name('billing.expired');
Route::get('/billing-locked', function () {
    return view('billing.locked');
})->name('billing.locked');

/*
|--------------------------------------------------------------------------
| TENANT / BUSINESS ROUTES (LOCKED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.subscription'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::delete('/customers/{customer}/delete', [CustomerController::class, 'destroy'])->name('customers.delete');

    Route::resource('quotes', QuoteController::class);
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'downloadPdf'])->name('quotes.download');
    Route::post('/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');

    Route::get('/invoices', [InvoiceController::class, 'history'])->name('invoice.history');
    Route::get('/invoice/create', [InvoiceController::class, 'showForm'])->name('invoice.create');
    Route::post('/invoice/send', [InvoiceController::class, 'send'])->name('invoice.send');

    Route::get('/invoice/edit/{invoice}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/invoice/update/{invoice}', [InvoiceController::class, 'update'])->name('invoice.update');

    Route::get('/invoice/{invoice}/download', [InvoiceController::class, 'downloadPdf'])->name('invoice.download');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');

    Route::get('/invoice/internal/{invoice}', [InvoiceController::class, 'view'])->name('invoice.view');

    Route::post('/invoice/send-email/{invoice_no}', [InvoiceController::class, 'sendEmail'])->name('invoice.send_email');
    Route::post('/invoice/send-sms/{invoice_no}', [InvoiceController::class, 'sendSms'])->name('invoice.send_sms');

    Route::post('/invoice/{invoice}/resend', [InvoiceController::class, 'resend'])->name('invoice.resend');
    Route::post('/invoice/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoice.markPaid');

    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::post('/company/settings', [CompanyController::class, 'update'])->name('company.update');

    Route::get('/company/users', [UserController::class, 'index'])->name('company.users');
    Route::get('/company/users/create', [UserController::class, 'create'])->name('company.users.create');
    Route::post('/company/users/store', [UserController::class, 'store'])->name('company.users.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'superadmin'])->prefix('admin')->group(function () {

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies');
    Route::get('/companies/create', [AdminController::class, 'createCompany'])->name('admin.companies.create');
    Route::post('/companies/store', [AdminController::class, 'storeCompany'])->name('admin.companies.store');
    Route::post('/companies/{id}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.toggleStatus');
    Route::delete('/companies/{id}', [AdminController::class, 'destroyCompany'])->name('admin.companies.destroy');

    Route::get('/brand', [CompanyController::class, 'settings'])->name('admin.brand');
    Route::post('/brand', [CompanyController::class, 'update'])->name('admin.brand.update');

    Route::post('/smtp/test', [CompanyController::class, 'testEmail'])->name('smtp.test');
});

Route::get('/impersonate/{id}', [AdminController::class, 'loginAsCompany'])->name('admin.impersonate');
Route::get('/stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('admin.stopImpersonating');

Route::get('/billing', [AdminController::class, 'billing'])->name('admin.billing');

Route::prefix('sales')->group(function () {
    Route::get('/overview', [SalesController::class, 'overview'])->name('admin.sales.overview');
    Route::get('/subscriptions', [SalesController::class, 'subscriptions'])->name('admin.sales.subscriptions');
    Route::get('/onboarding', [SalesController::class, 'onboarding'])->name('admin.sales.onboarding');
    Route::get('/promos', [SalesController::class, 'promos'])->name('admin.sales.promos');
});

require __DIR__.'/auth.php';
