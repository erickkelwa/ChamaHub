<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contribution;
use App\Models\Loan;
use Carbon\Carbon;

class DividendController extends Controller
{
    /**
     * Display the End-of-Year Dividend Calculator.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));

        // 1. Total Chama Contributions for the year
        // We look at 'paid_at' year for accurately capturing when the money came in.
        $totalChamaContributions = Contribution::whereIn('status', ['paid', 'partial'])
            ->whereYear('paid_at', $year)
            ->sum('amount_paid');

        // 2. Total Profit Pool (From fully repaid loans in the year)
        // Profit = total_repayable - amount_approved
        $repaidLoans = Loan::where('status', 'repaid')
            ->whereYear('updated_at', $year)
            ->get();

        $totalProfitPool = $repaidLoans->sum(function ($loan) {
            return $loan->total_repayable - $loan->amount_approved;
        });

        // 3. Member Contributions & Dividend Share
        $members = User::all();

        $memberDividends = collect();

        foreach ($members as $member) {
            $memberTotalContribution = Contribution::where('user_id', $member->id)
                ->whereIn('status', ['paid', 'partial'])
                ->whereYear('paid_at', $year)
                ->sum('amount_paid');

            $dividendSharePercent = 0;
            if ($totalChamaContributions > 0) {
                $dividendSharePercent = ($memberTotalContribution / $totalChamaContributions) * 100;
            }

            $dividendAmount = ($dividendSharePercent / 100) * $totalProfitPool;

            if ($memberTotalContribution > 0) { // Only include members who actually contributed this year
                $memberDividends->push((object)[
                    'user' => $member,
                    'total_contribution' => $memberTotalContribution,
                    'share_percent' => $dividendSharePercent,
                    'dividend_amount' => $dividendAmount,
                ]);
            }
        }

        // Sort by highest contribution
        $memberDividends = $memberDividends->sortByDesc('total_contribution')->values();

        // Get available years for the dropdown (database agnostic for MySQL & PostgreSQL)
        $availableYears = Contribution::whereNotNull('paid_at')
                            ->pluck('paid_at')
                            ->map(function ($date) {
                                return Carbon::parse($date)->format('Y');
                            })
                            ->unique()
                            ->values()
                            ->toArray();
        
        // Ensure the currently selected year and current actual year are in the list
        if (!in_array(date('Y'), $availableYears)) {
            array_unshift($availableYears, date('Y'));
        }
        if (!in_array((string)$year, $availableYears)) {
            $availableYears[] = (string)$year;
        }
        rsort($availableYears);

        return view('admin.dividends.index', compact(
            'year',
            'totalChamaContributions',
            'totalProfitPool',
            'memberDividends',
            'availableYears'
        ));
    }
}
