<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\MeetingAttendance;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateFinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chama:generate-fines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate fines for late contributions, late loans, and missed meetings.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting automated fine generation...');
        $count = 0;

        // 1. Late Contributions (Ksh 200)
        $lateContributions = Contribution::whereIn('status', ['unpaid', 'partial'])
            ->where('created_at', '<', Carbon::now()->subDays(35))
            ->get();

        foreach ($lateContributions as $contrib) {
            $reason = "Late Contribution for {$contrib->month}";
            $exists = Fine::where('user_id', $contrib->user_id)
                ->where('type', 'late_contribution')
                ->where('reason', $reason)
                ->exists();

            if (!$exists) {
                Fine::create([
                    'user_id' => $contrib->user_id,
                    'amount' => 200,
                    'reason' => $reason,
                    'type' => 'late_contribution',
                ]);
                $count++;
                $this->line("Created late contribution fine for User ID {$contrib->user_id}");
            }
        }

        // 2. Late Loans (Ksh 500)
        $activeLoans = Loan::whereIn('status', ['approved', 'defaulted'])->get();
        foreach ($activeLoans as $loan) {
            if (!$loan->approved_at) continue;
            
            $dueDate = Carbon::parse($loan->approved_at)->addMonths($loan->repayment_months);
            if (Carbon::now()->greaterThan($dueDate)) {
                $reason = "Late Loan Repayment (Loan #{$loan->id})";
                $exists = Fine::where('user_id', $loan->user_id)
                    ->where('type', 'late_loan')
                    ->where('reason', $reason)
                    ->exists();

                if (!$exists) {
                    Fine::create([
                        'user_id' => $loan->user_id,
                        'amount' => 500,
                        'reason' => $reason,
                        'type' => 'late_loan',
                    ]);
                    
                    if ($loan->status !== 'defaulted') {
                        $loan->update(['status' => 'defaulted']);
                    }
                    $count++;
                    $this->line("Created late loan fine for User ID {$loan->user_id}");
                }
            }
        }

        // 3. Missed Meetings (Ksh 500)
        $absences = MeetingAttendance::where('status', 'absent')->with('meeting')->get();
        foreach ($absences as $absence) {
            if (!$absence->meeting) continue;
            
            $reason = "Missed Meeting: {$absence->meeting->title} on " . Carbon::parse($absence->meeting->meeting_date)->format('Y-m-d');
            $exists = Fine::where('user_id', $absence->user_id)
                ->where('type', 'meeting_absence')
                ->where('reason', $reason)
                ->exists();

            if (!$exists) {
                Fine::create([
                    'user_id' => $absence->user_id,
                    'amount' => 500,
                    'reason' => $reason,
                    'type' => 'meeting_absence',
                ]);
                $count++;
                $this->line("Created missed meeting fine for User ID {$absence->user_id}");
            }
        }

        $this->info("Completed. Generated {$count} new fines.");
        return Command::SUCCESS;
    }
}
