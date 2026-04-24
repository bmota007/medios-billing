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
use Illuminate\Http\Request;
use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\PricingController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    /*
    |--------------------------------------------------------------------------
    | If logged in already
    |--------------------------------------------------------------------------
    */
    if (auth()->check()) {

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ALWAYS BYPASS BILLING
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL USERS GO TO DASHBOARD
        |--------------------------------------------------------------------------
        */
        return redirect()->route('dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Guests always see login page
    |--------------------------------------------------------------------------
    */
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| FORCE LOGIN PAGE DIRECT ACCESS
|--------------------------------------------------------------------------
*/
Route::get('/login-direct', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| PUBLIC SMS WEBHOOK ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/sms/webhook', [SmsWebhookController::class, 'handle']);

Route::get('/invoice/{id}/send-sms', [
    App\Http\Controllers\InvoiceController::class,
    'sendInvoiceSms'
])->name('invoice.send.sms');

/*
|--------------------------------------------------------------------------
| STRIPE WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| PUBLIC INVOICE & PAYMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/invoice/view/{invoice_no}', [InvoiceController::class, 'publicView'])
    ->name('invoice.public_view');

Route::get('/invoice/pay/{invoice_no}', [InvoiceController::class, 'showPaymentPage'])
    ->name('invoice.pay');

Route::get('/invoice/checkout/{invoice_no}', [InvoiceController::class, 'stripeCheckout'])
    ->name('invoice.checkout');

Route::get('/invoice/success/{invoice_no}', [InvoiceController::class, 'stripeSuccess'])
    ->name('stripe.success');

Route::post('/invoice/{invoice_no}/manual-payment', [InvoiceController::class, 'submitManualPayment'])
    ->name('invoice.manual.payment');

Route::get('/invoice/{invoice_no}/success', [InvoiceController::class, 'paymentSuccess'])
    ->name('invoice.payment.success');

Route::put('/invoice/update/{invoice}', [InvoiceController::class, 'update'])
    ->name('invoice.update');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| PUBLIC QUOTES
|--------------------------------------------------------------------------
*/
Route::get('/q/{token}', [QuoteController::class, 'publicView'])
    ->name('quotes.public');

Route::post('/q/{token}/approve', [QuoteController::class, 'approve'])
    ->name('quotes.approve');

Route::get('/q/{token}/contract', [QuoteController::class, 'showContract'])
    ->name('quotes.contract');

Route::post('/q/{token}/contract/sign', [QuoteController::class, 'signContract'])
    ->name('quotes.contract.sign');

Route::get('/quote/{id}/contract-view', [QuoteController::class, 'viewContractFile'])
    ->name('quotes.contract.view');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/logout', function () {

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

    Route::post('/process-subscription', [BillingController::class, 'processSubscription'])
        ->name('process.subscription');

    Route::post('/billing/pay', [BillingController::class, 'processSubscription'])
        ->name('billing.pay');

    Route::get('/checkout/subscribe/{companyId}', [CheckoutController::class, 'subscribe'])
        ->name('checkout.subscribe');

/*
|--------------------------------------------------------------------------
| CUSTOMER BILLING CENTER
|--------------------------------------------------------------------------
*/
Route::get('/subscription', [CheckoutController::class, 'portal'])
    ->name('subscription.portal');

Route::post('/subscription/cancel', [CheckoutController::class, 'cancel'])
    ->name('subscription.cancel');

Route::post('/subscription/change-plan', [CheckoutController::class, 'changePlan'])
    ->name('subscription.changePlan');

Route::post('/subscription/reactivate', [CheckoutController::class, 'reactivate'])
    ->name('subscription.reactivate');
});

Route::get('/billing/expired', [BillingController::class, 'expired'])
    ->name('billing.expired');

Route::get('/billing-locked', function () {
    return view('billing.locked');
})->name('billing.locked');

/*
|--------------------------------------------------------------------------
| TENANT / BUSINESS ROUTES (LOCKED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.subscription'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('quotes', QuoteController::class);

    Route::get('/quotes/{quote}/download', [QuoteController::class, 'downloadPdf'])
        ->name('quotes.download');

    Route::post('/quotes/{quote}/send', [QuoteController::class, 'send'])
        ->name('quotes.send');

    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])
        ->name('quotes.convert');

    Route::get('/invoices', [InvoiceController::class, 'history'])
        ->name('invoice.history');

    Route::get('/invoice/create', [InvoiceController::class, 'showForm'])
        ->name('invoice.create');

    Route::post('/invoice/send', [InvoiceController::class, 'send'])
        ->name('invoice.send');

    Route::get('/invoice/edit/{invoice}', [InvoiceController::class, 'edit'])
        ->name('invoice.edit');

    Route::put('/invoice/update/{invoice}', [InvoiceController::class, 'update'])
        ->name('invoice.update');

    Route::get('/invoice/{invoice}/download', [InvoiceController::class, 'downloadPdf'])
        ->name('invoice.download');

    Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])
        ->name('invoice.destroy');

    Route::get('/invoice/internal/{invoice}', [InvoiceController::class, 'view'])
        ->name('invoice.view');

    Route::post('/invoice/send-email/{invoice_no}', [InvoiceController::class, 'sendEmail'])
        ->name('invoice.send_email');

    Route::post('/invoice/send-sms/{invoice_no}', [InvoiceController::class, 'sendSms'])
        ->name('invoice.send_sms');

    Route::post('/invoice/{invoice}/resend', [InvoiceController::class, 'resend'])
        ->name('invoice.resend');

    Route::post('/invoice/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])
        ->name('invoice.markPaid');

    Route::get('/company/settings', [CompanyController::class, 'settings'])
        ->name('company.settings');

    Route::post('/company/settings', [CompanyController::class, 'update'])
        ->name('company.update');

    Route::get('/company/users', [UserController::class, 'index'])
        ->name('company.users');

    Route::get('/company/users/create', [UserController::class, 'create'])
        ->name('company.users.create');

    Route::post('/company/users/store', [UserController::class, 'store'])
        ->name('company.users.store');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/plans', [App\Http\Controllers\AdminPlanController::class, 'index'])
            ->name('admin.plans.index');

        Route::post('/plans/{id}', [App\Http\Controllers\AdminPlanController::class, 'update'])
            ->name('admin.plans.update');

        Route::get('/companies', [AdminController::class, 'companies'])
            ->name('admin.companies');

        Route::get('/companies/create', [AdminController::class, 'createCompany'])
            ->name('admin.companies.create');

        Route::post('/companies/store', [AdminController::class, 'storeCompany'])
            ->name('admin.companies.store');

        Route::post('/companies/{id}/toggle', [AdminController::class, 'toggleStatus'])
            ->name('admin.toggleStatus');

        Route::delete('/companies/{id}', [AdminController::class, 'destroyCompany'])
            ->name('admin.companies.destroy');

        Route::get('/brand', [CompanyController::class, 'settings'])
            ->name('admin.brand');

        Route::post('/brand', [CompanyController::class, 'update'])
            ->name('admin.brand.update');

        Route::post('/smtp/test', [CompanyController::class, 'testEmail'])
            ->name('smtp.test');
    });

Route::post('/admin/leads', [AdminController::class, 'storeLead'])
    ->name('admin.leads.store');

Route::get('/impersonate/{id}', [AdminController::class, 'loginAsCompany'])
    ->name('admin.impersonate');

Route::get('/stop-impersonating', [AdminController::class, 'stopImpersonating'])
    ->name('admin.stopImpersonating');

Route::get('/billing', [AdminController::class, 'billing'])
    ->name('admin.billing');

Route::prefix('sales')->group(function () {

    Route::get('/overview', [SalesController::class, 'overview'])
        ->name('admin.sales.overview');

    Route::get('/subscriptions', [SalesController::class, 'subscriptions'])
        ->name('admin.sales.subscriptions');

    Route::get('/onboarding', [SalesController::class, 'onboarding'])
        ->name('admin.sales.onboarding');

    Route::get('/promos', [SalesController::class, 'promos'])
        ->name('admin.sales.promos');
});

require __DIR__.'/auth.php';
