<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\LoanApprovedNotification;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the loans.
     */
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'approver']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $loans = $query->latest()->paginate(15);
        return view('admin.loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan (manual entry by admin).
     */
    public function create()
    {
        $members = User::whereIn('role', ['member', 'treasurer', 'admin'])->get();
        return view('admin.loans.create', compact('members'));
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount_requested' => 'required|numeric|min:0',
            'amount_approved' => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'repayment_months' => 'required|integer|min:1',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected,repaid,defaulted',
        ]);

        $data = $request->all();
        
        if ($data['status'] == 'approved' && empty($data['approved_by'])) {
            $data['approved_by'] = auth()->id();
            $data['approved_at'] = now();
            // Calculate total repayable if approved
            if (!empty($data['amount_approved'])) {
                $interest = ($data['amount_approved'] * $data['interest_rate']) / 100;
                $data['total_repayable'] = $data['amount_approved'] + $interest;
                $data['balance'] = $data['total_repayable'];
            }
        }

        Loan::create($data);

        return redirect()->route('admin.loans.index')->with('success', 'Loan record created successfully.');
    }

    /**
     * Show the form for editing the specified loan.
     */
    public function edit($id)
    {
        $loan = Loan::findOrFail($id);
        $members = User::all();
        return view('admin.loans.edit', compact('loan', 'members'));
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $request->validate([
            'amount_approved' => 'nullable|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected,repaid,defaulted',
            'rejection_note' => 'nullable|string'
        ]);

        $data = $request->all();

        // Handle approval logic
        if ($data['status'] == 'approved' && $loan->status != 'approved') {
            $data['approved_by'] = auth()->id();
            $data['approved_at'] = now();
            if (!empty($data['amount_approved'])) {
                $interest = ($data['amount_approved'] * $data['interest_rate']) / 100;
                $data['total_repayable'] = $data['amount_approved'] + $interest;
                $data['balance'] = $data['total_repayable'] - $loan->amount_repaid;
            }
        }

        $wasApproved = $loan->status != 'approved' && $data['status'] == 'approved';
        $loan->update($data);

        // Notify member when loan is first approved
        if ($wasApproved) {
            $loan->refresh();
            \App\Models\Notification::create([
                'user_id' => $loan->user_id,
                'type'    => 'loan_approved',
                'title'   => 'Loan Application Approved 🎉',
                'message' => 'Your loan application of Ksh ' . number_format($loan->amount_approved, 2) . ' has been approved.',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            $loan->user->notify(new LoanApprovedNotification($loan));
        }

        return redirect()->route('admin.loans.index')->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified loan from storage.
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return redirect()->route('admin.loans.index')->with('success', 'Loan deleted successfully.');
    }
}
