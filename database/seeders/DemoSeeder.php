<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\Fine;
use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Create System Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@chamahub.com'],
            [
                'name' => 'System Admin',
                'phone' => '0700000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // 2. Create Treasurer
        $treasurer = User::updateOrCreate(
            ['email' => 'treasurer@chamahub.com'],
            [
                'name' => 'Jane Wanjiku (Treasurer)',
                'phone' => '0712345678',
                'password' => Hash::make('password'),
                'role' => 'treasurer',
                'status' => 'active',
            ]
        );

        // 3. Create Demo Members
        $membersData = [
            ['name' => 'David Omondi', 'email' => 'david@chamahub.com', 'phone' => '0721111222'],
            ['name' => 'Grace Njeri', 'email' => 'grace@chamahub.com', 'phone' => '0722222333'],
            ['name' => 'Peter Kamau', 'email' => 'peter@chamahub.com', 'phone' => '0723333444'],
            ['name' => 'Faith Mutua', 'email' => 'faith@chamahub.com', 'phone' => '0724444555'],
            ['name' => 'Brian Kiprop', 'email' => 'brian@chamahub.com', 'phone' => '0725555666'],
            ['name' => 'Mercy Achieng', 'email' => 'mercy@chamahub.com', 'phone' => '0726666777'],
            ['name' => 'Samuel Hassan', 'email' => 'samuel@chamahub.com', 'phone' => '0727777888'],
            ['name' => 'Sarah Chebet', 'email' => 'sarah@chamahub.com', 'phone' => '0728888999'],
        ];

        $members = [];
        foreach ($membersData as $data) {
            $members[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'member',
                    'status' => 'active',
                ]
            );
        }

        // Add any pre-existing users to member list if any
        $allMembers = User::where('role', 'member')->get();

        // 4. Seed Monthly Contributions (Jan - Aug 2026)
        $months = [
            '2026-01', '2026-02', '2026-03', '2026-04',
            '2026-05', '2026-06', '2026-07', '2026-08'
        ];

        $monthlyAmount = 5000;

        foreach ($allMembers as $index => $member) {
            foreach ($months as $mIndex => $month) {
                // Determine status based on month and member index for realistic variation
                if ($mIndex < 6) {
                    $status = 'paid';
                    $paidAmount = $monthlyAmount;
                    $paidAt = Carbon::parse("$month-10");
                } elseif ($mIndex === 6) { // July
                    $status = ($index % 2 === 0) ? 'paid' : 'partial';
                    $paidAmount = ($status === 'paid') ? $monthlyAmount : 2500;
                    $paidAt = ($status === 'paid') ? Carbon::parse("$month-12") : Carbon::parse("$month-20");
                } else { // August (Current month)
                    $status = ($index < 3) ? 'paid' : (($index < 6) ? 'partial' : 'unpaid');
                    $paidAmount = ($status === 'paid') ? $monthlyAmount : (($status === 'partial') ? 2000 : 0);
                    $paidAt = ($paidAmount > 0) ? Carbon::now()->subDays($index * 2) : null;
                }

                Contribution::updateOrCreate(
                    [
                        'user_id' => $member->id,
                        'month' => $month,
                    ],
                    [
                        'amount_due' => $monthlyAmount,
                        'amount_paid' => $paidAmount,
                        'status' => $status,
                        'paid_at' => $paidAt,
                    ]
                );
            }
        }

        // 5. Seed Loans
        if ($allMembers->count() >= 4) {
            // Approved loan 1
            Loan::updateOrCreate(
                ['user_id' => $allMembers[0]->id, 'reason' => 'Expansion of Hardware Store Business'],
                [
                    'amount_requested' => 100000,
                    'amount_approved' => 100000,
                    'interest_rate' => 10,
                    'total_repayable' => 110000,
                    'amount_repaid' => 45000,
                    'balance' => 65000,
                    'status' => 'approved',
                    'repayment_months' => 12,
                    'approved_by' => $admin->id,
                    'approved_at' => Carbon::now()->subMonths(3),
                ]
            );

            // Approved loan 2
            Loan::updateOrCreate(
                ['user_id' => $allMembers[1]->id, 'reason' => 'University Tuition Fees for First Semester'],
                [
                    'amount_requested' => 50000,
                    'amount_approved' => 50000,
                    'interest_rate' => 8,
                    'total_repayable' => 54000,
                    'amount_repaid' => 27000,
                    'balance' => 27000,
                    'status' => 'approved',
                    'repayment_months' => 6,
                    'approved_by' => $admin->id,
                    'approved_at' => Carbon::now()->subMonths(2),
                ]
            );

            // Pending loan 1
            Loan::updateOrCreate(
                ['user_id' => $allMembers[2]->id, 'reason' => 'Purchase of Dairy Farming Equipment'],
                [
                    'amount_requested' => 75000,
                    'amount_approved' => null,
                    'interest_rate' => 10,
                    'total_repayable' => null,
                    'amount_repaid' => 0,
                    'balance' => null,
                    'status' => 'pending',
                    'repayment_months' => 12,
                ]
            );

            // Pending loan 2
            Loan::updateOrCreate(
                ['user_id' => $allMembers[3]->id, 'reason' => 'Emergency Medical Expense'],
                [
                    'amount_requested' => 30000,
                    'amount_approved' => null,
                    'interest_rate' => 5,
                    'total_repayable' => null,
                    'amount_repaid' => 0,
                    'balance' => null,
                    'status' => 'pending',
                    'repayment_months' => 6,
                ]
            );
        }

        // 6. Seed Meetings
        $pastMeeting = Meeting::updateOrCreate(
            ['title' => 'Chama General Monthly Meeting - July 2026'],
            [
                'agenda' => 'Review of Q2 Financial Reports, Dividend Allocations & New Loan Applications',
                'venue' => 'Chama Community Center Hall B / Zoom Hybrid',
                'meeting_date' => Carbon::now()->subDays(20),
                'minutes' => 'Meeting called to order at 2:00 PM. Financial statement presented by Treasurer. Q2 loan approvals confirmed unanimously.',
                'created_by' => $admin->id,
            ]
        );

        $upcomingMeeting = Meeting::updateOrCreate(
            ['title' => 'Chama Strategic Investment Meeting - August 2026'],
            [
                'agenda' => 'Deliberation on purchasing Chama plot land investment in Juja & M-Pesa Integration review.',
                'venue' => 'Nairobi Club & Online Stream',
                'meeting_date' => Carbon::now()->addDays(5),
                'minutes' => null,
                'created_by' => $admin->id,
            ]
        );

        // 7. Seed Fines
        if ($allMembers->count() >= 3) {
            Fine::updateOrCreate(
                ['user_id' => $allMembers[4]->id, 'reason' => 'Late Monthly Contribution Submission for July'],
                [
                    'amount' => 500,
                    'type' => 'late_contribution',
                    'status' => 'unpaid',
                ]
            );

            Fine::updateOrCreate(
                ['user_id' => $allMembers[5]->id, 'reason' => 'Unexcused Absence from July General Meeting'],
                [
                    'amount' => 300,
                    'type' => 'meeting_absence',
                    'status' => 'paid',
                    'paid_at' => Carbon::now()->subDays(10),
                ]
            );

            Fine::updateOrCreate(
                ['user_id' => $allMembers[6]->id, 'reason' => 'Late Loan Installment Payment'],
                [
                    'amount' => 1000,
                    'type' => 'late_loan',
                    'status' => 'waived',
                    'waived_at' => Carbon::now()->subDays(5),
                    'waived_reason' => 'Valid medical emergency excuse submitted',
                ]
            );
        }

        // 8. Seed Decision Polls
        $poll1 = Poll::updateOrCreate(
            ['title' => 'Proposal: Increase Monthly Savings Contribution to KES 6,000'],
            [
                'description' => 'Should we increase our baseline monthly contribution from KES 5,000 to KES 6,000 starting September 2026 to boost our loan pool?',
                'created_by' => $admin->id,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(10),
            ]
        );

        $poll2 = Poll::updateOrCreate(
            ['title' => 'Investment Choice: Acquire 1/4 Acre Commercial Plot in Juja'],
            [
                'description' => 'Vote on allocating KES 1.2M from collective Chama savings to acquire commercial plot in Juja town.',
                'created_by' => $admin->id,
                'status' => 'closed',
                'expires_at' => Carbon::now()->subDays(2),
            ]
        );
    }
}
