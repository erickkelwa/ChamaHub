<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'landing']);

Route::get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::resource('admin/members', MemberController::class)->names([
            'index' => 'admin.members.index',
            'create' => 'admin.members.create',
            'store' => 'admin.members.store',
            'show' => 'admin.members.show',
            'edit' => 'admin.members.edit',
            'update' => 'admin.members.update',
            'destroy' => 'admin.members.destroy',
        ]);
        
        Route::resource('admin/contributions', \App\Http\Controllers\Admin\ContributionController::class)->names([
            'index' => 'admin.contributions.index',
            'create' => 'admin.contributions.create',
            'store' => 'admin.contributions.store',
            'show' => 'admin.contributions.show',
            'edit' => 'admin.contributions.edit',
            'update' => 'admin.contributions.update',
            'destroy' => 'admin.contributions.destroy',
        ]);
        
        Route::resource('admin/loans', \App\Http\Controllers\Admin\LoanController::class)->names([
            'index' => 'admin.loans.index',
            'create' => 'admin.loans.create',
            'store' => 'admin.loans.store',
            'show' => 'admin.loans.show',
            'edit' => 'admin.loans.edit',
            'update' => 'admin.loans.update',
            'destroy' => 'admin.loans.destroy',
        ]);
        
        Route::resource('admin/meetings', \App\Http\Controllers\Admin\MeetingController::class)->names([
            'index' => 'admin.meetings.index',
            'create' => 'admin.meetings.create',
            'store' => 'admin.meetings.store',
            'show' => 'admin.meetings.show',
            'edit' => 'admin.meetings.edit',
            'update' => 'admin.meetings.update',
            'destroy' => 'admin.meetings.destroy',
        ]);
        
        // Reports
        Route::get('admin/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('admin/reports/reminders', [\App\Http\Controllers\Admin\ReportController::class, 'sendReminders'])->name('admin.reports.reminders');
        
        // Automated Schedule Generator
        Route::post('admin/contributions/generate-schedule', [\App\Http\Controllers\Admin\ContributionController::class, 'generateSchedule'])->name('admin.contributions.generate-schedule');
    });

    // M-Pesa STK Push initiation (authenticated)
    Route::post('mpesa/push', [\App\Http\Controllers\MpesaController::class, 'initiatePush'])->name('mpesa.push');
    Route::post('mpesa/deposit', [\App\Http\Controllers\MpesaController::class, 'initiateDeposit'])->name('mpesa.deposit');
});

// M-Pesa callback — must be public (Safaricom cannot authenticate)
Route::post('mpesa/callback', [\App\Http\Controllers\MpesaController::class, 'callback'])->name('mpesa.callback');

require __DIR__.'/auth.php';
