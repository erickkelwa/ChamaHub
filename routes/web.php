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
    
    // Decisions
    Route::get('/decisions', [\App\Http\Controllers\PollController::class, 'index'])->name('decisions.index');
    Route::post('/decisions/{poll}/vote', [\App\Http\Controllers\PollController::class, 'vote'])->name('decisions.vote');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    
    // Member Loan Application
    Route::post('/loan-applications', [\App\Http\Controllers\LoanApplicationController::class, 'store'])->name('loan.applications.store');

    // Meetings (read-only for all members)
    Route::get('/meetings', [\App\Http\Controllers\Admin\MeetingController::class, 'index'])->name('admin.meetings.index');
    Route::get('/meetings/{id}', [\App\Http\Controllers\Admin\MeetingController::class, 'show'])->name('admin.meetings.show');
    
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
        
        // Meetings (admin management only)
        Route::get('admin/meetings/create', [\App\Http\Controllers\Admin\MeetingController::class, 'create'])->name('admin.meetings.create');
        Route::post('admin/meetings', [\App\Http\Controllers\Admin\MeetingController::class, 'store'])->name('admin.meetings.store');
        Route::get('admin/meetings/{id}/edit', [\App\Http\Controllers\Admin\MeetingController::class, 'edit'])->name('admin.meetings.edit');
        Route::put('admin/meetings/{id}', [\App\Http\Controllers\Admin\MeetingController::class, 'update'])->name('admin.meetings.update');
        Route::delete('admin/meetings/{id}', [\App\Http\Controllers\Admin\MeetingController::class, 'destroy'])->name('admin.meetings.destroy');
        Route::post('admin/meetings/{meeting}/attendance', [\App\Http\Controllers\Admin\MeetingController::class, 'saveAttendance'])->name('admin.meetings.attendance');
        
        // Reports
        Route::get('admin/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('admin/reports/reminders', [\App\Http\Controllers\Admin\ReportController::class, 'sendReminders'])->name('admin.reports.reminders');
        
        // Dividends Calculator
        Route::get('admin/dividends', [\App\Http\Controllers\Admin\DividendController::class, 'index'])->name('admin.dividends.index');
        
        // Fines
        Route::get('admin/fines', [\App\Http\Controllers\Admin\FineController::class, 'index'])->name('admin.fines.index');
        Route::patch('admin/fines/{fine}/pay', [\App\Http\Controllers\Admin\FineController::class, 'markAsPaid'])->name('admin.fines.pay');
        Route::patch('admin/fines/{fine}/waive', [\App\Http\Controllers\Admin\FineController::class, 'waive'])->name('admin.fines.waive');
        
        // Decisions Admin
        Route::post('admin/decisions', [\App\Http\Controllers\PollController::class, 'store'])->name('admin.decisions.store');
        Route::patch('admin/decisions/{poll}/close', [\App\Http\Controllers\PollController::class, 'close'])->name('admin.decisions.close');
        
        // Automated Schedule Generator
        Route::post('admin/contributions/generate-schedule', [\App\Http\Controllers\Admin\ContributionController::class, 'generateSchedule'])->name('admin.contributions.generate-schedule');
        Route::post('admin/contributions/bulk-stk-push', [\App\Http\Controllers\Admin\ContributionController::class, 'bulkStkPush'])->name('admin.contributions.bulk-stk-push');

        // Seed Demo Members Action
        Route::post('admin/seed-demo', function () {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
            return redirect()->back()->with('success', '10 Demo members and financial data populated successfully!');
        })->name('admin.seed-demo');
    });

    // M-Pesa STK Push initiation (authenticated)
    Route::post('mpesa/push', [\App\Http\Controllers\MpesaController::class, 'initiatePush'])->name('mpesa.push');
    Route::post('mpesa/deposit', [\App\Http\Controllers\MpesaController::class, 'initiateDeposit'])->name('mpesa.deposit');
});

// 1-Click Public/Admin Demo Seeder Trigger
Route::get('/seed-demo', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'DemoSeeder',
            '--force' => true
        ]);
        return redirect('/dashboard')->with('success', 'Demo members, contributions, loans, and meetings successfully populated!');
    } catch (\Throwable $e) {
        return redirect('/dashboard')->with('error', 'Seeding notice: ' . $e->getMessage());
    }
})->name('seed-demo');

require __DIR__.'/auth.php';

