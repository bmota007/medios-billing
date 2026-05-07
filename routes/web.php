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
use App\Http\Controllers\BillingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SmsWebhookController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\Billing\CheckoutController;

/* CORE AUTH & PUBLIC */
Route::get('/', function () {
    if (auth()->check()) {
        return (auth()->user()->role === 'super_admin') ? redirect()->route('admin.dashboard') : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login-direct', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
});

Route::post('/sms/webhook', [SmsWebhookController::class, 'handle']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

/* PUBLIC INVOICE & PAYMENT */
Route::get('/invoice/view/{invoice_no}', [InvoiceController::class, 'publicView'])->name('invoice.public_view');
Route::get('/invoice/pay/{invoice_no}', [InvoiceController::class, 'showPaymentPage'])->name('invoice.pay');
Route::get('/invoice/checkout/{invoice_no}', [InvoiceController::class, 'stripeCheckout'])->name('invoice.checkout');
Route::get('/invoice/success/{invoice_no}', [InvoiceController::class, 'stripeSuccess'])->name('stripe.success');
Route::post('/invoice/{invoice_no}/manual-payment', [InvoiceController::class, 'submitManualPayment'])->name('invoice.manual.payment');
Route::get('/invoice/{invoice_no}/success', [InvoiceController::class, 'paymentSuccess'])->name('invoice.payment.success');

/* PUBLIC QUOTE FLOW (High-End White UI) */
Route::get('/q/{token}', [QuoteController::class, 'publicView'])->name('quotes.public');
Route::get('/q/{token}/download', [QuoteController::class, 'publicDownloadPdf'])->name('quotes.public_download');
Route::post('/q/{token}/approve', [QuoteController::class, 'approve'])->name('quotes.approve');
Route::get('/q/{token}/contract', [QuoteController::class, 'showContract'])->name('quotes.contract');
Route::post('/q/{token}/contract/sign', [QuoteController::class, 'signContract'])->name('quotes.contract.sign');

Route::match(['get', 'post'], '/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/* BILLING */
Route::middleware('auth')->group(function () {
    Route::get('/subscribe', function () { return view('billing.subscribe'); })->name('subscribe');
    Route::post('/process-subscription', [BillingController::class, 'processSubscription'])->name('process.subscription');
    Route::get('/subscription', [CheckoutController::class, 'portal'])->name('subscription.portal');
    Route::get('/billing/success', [CheckoutController::class, 'success'])->name('billing.success');
});
Route::get('/billing-locked', function () { return view('billing.locked'); })->name('billing.locked');

/* TENANT ROUTES (Black Dashboard) */
Route::middleware(['auth', 'check.subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('quotes', QuoteController::class);
    
    Route::get('/quotes/{quote}/pro-preview', [QuoteController::class, 'preview'])->name('quotes.pro_preview');
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'downloadPdf'])->name('quotes.download');
    Route::post('/quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('/quotes/{quote}/send-sms', [QuoteController::class, 'sendSms'])->name('quotes.send_sms');
    Route::post('/quotes/{quote}/mark-paid', [QuoteController::class, 'markPaid'])->name('quotes.markPaid');
    Route::post('/quotes/{quote}/mark-deposit', [QuoteController::class, 'markDeposit'])->name('quotes.markDeposit');
    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    
    Route::get('/invoices', [InvoiceController::class, 'history'])->name('invoice.history');
    Route::get('/invoice/create', [InvoiceController::class, 'showForm'])->name('invoice.create');
    Route::post('/invoice/send', [InvoiceController::class, 'send'])->name('invoice.send');
Route::post('/invoice/send-existing', [InvoiceController::class, 'sendExisting'])->name('invoice.send.existing'); 
   Route::get('/invoice/edit/{invoice}', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('/invoice/internal/{invoice}', [InvoiceController::class, 'view'])->name('invoice.view');
Route::get('/invoice/pdf/{invoice}', [InvoiceController::class, 'pdf'])->name('invoice.pdf');   
 Route::post('/invoice/{invoice}/resend', [InvoiceController::class, 'resend'])->name('invoice.resend');

    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::post('/company/settings', [CompanyController::class, 'update'])->name('company.update');
    
    // RESTORED: Team Users for Sidebars
    Route::get('/company/users', [UserController::class, 'index'])->name('company.users');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/* SUPER ADMIN */
Route::middleware(['auth', 'superadmin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies');
    Route::get('/companies/create', [AdminController::class, 'createCompany'])->name('admin.companies.create');
    Route::post('/companies/store', [AdminController::class, 'storeCompany'])->name('admin.companies.store');
    Route::get('/billing', [AdminController::class, 'billing'])->name('admin.billing');
    Route::post('/leads', [AdminController::class, 'storeLead'])->name('admin.leads.store');
    
    // RESTORED: Admin Branding
    Route::get('/brand', [CompanyController::class, 'settings'])->name('admin.brand');
    
    Route::prefix('sales')->group(function () {
        Route::get('/overview', [SalesController::class, 'overview'])->name('admin.sales.overview');
        Route::get('/subscriptions', [SalesController::class, 'subscriptions'])->name('admin.sales.subscriptions');
        Route::get('/onboarding', [SalesController::class, 'onboarding'])->name('admin.sales.onboarding');
        // ✅ RESTORED: Missing Promos Route
        Route::get('/promos', [SalesController::class, 'promos'])->name('admin.sales.promos');
    });
});

Route::get('/impersonate/{id}', [AdminController::class, 'loginAsCompany'])->name('admin.impersonate');
Route::get('/stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('admin.stopImpersonating');

require __DIR__.'/auth.php';

// Quote Public Viewing & Signing Routes
Route::get('/q/{token}/contract', [App\Http\Controllers\QuoteController::class, 'showContract'])->name('quotes.contract');
Route::post('/q/{token}/sign', [App\Http\Controllers\QuoteController::class, 'sign'])->name('quotes.sign');

// Public Quote & Payment Routes
Route::get('/q/{token}', [App\Http\Controllers\QuoteController::class, 'publicView'])->name('quotes.public_view');

// Invoice & Stripe Payment Routes
Route::get('/invoices/{id}/pay', [App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoices.pay');
Route::post('/invoices/{id}/stripe', [App\Http\Controllers\InvoiceController::class, 'stripePost'])->name('invoices.stripe.post');

// Restore missing SMTP test route
Route::post('/admin/smtp-test', [App\Http\Controllers\CompanyController::class, 'testSMTP'])->name('smtp.test');

// Restore missing settings and payment routes
Route::post('/admin/smtp-test', [App\Http\Controllers\CompanyController::class, 'testSMTP'])->name('smtp.test');
Route::get('/invoices/{id}/pay', [App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoices.pay');

// 🔥 BRAND SETTINGS (STRIPE + LOGO FIX)
Route::post('/company/settings/branding', [App\Http\Controllers\CompanyController::class, 'updateBranding'])->name('company.branding.update');
