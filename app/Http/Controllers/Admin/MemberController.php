<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request)
    {
        $query = User::withSum(['contributions as total_savings' => function($q) {
            $q->where('status', 'paid');
        }], 'amount_paid');

        // Search logic
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(15);
        return view('admin.members.index', compact('members'));
    }

    /**
     * Display the specified member details, savings, and deposits history.
     */
    public function show($id)
    {
        $member = User::findOrFail($id);

        $totalSavings = $member->contributions()->where('status', 'paid')->sum('amount_paid');

        $pendingDues = $member->contributions()
            ->where('status', '!=', 'paid')
            ->selectRaw('SUM(amount_due - amount_paid) as pending')
            ->value('pending') ?? 0;

        $activeLoan = $member->loans()->where('status', 'approved')->first();

        $contributions = $member->contributions()->with('payments')->latest()->paginate(10);

        return view('admin.members.show', compact('member', 'totalSavings', 'pendingDues', 'activeLoan', 'contributions'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        return view('admin.members.create');
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15'],
            'role' => ['required', 'in:admin,treasurer,member'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.members.index')->with('success', 'Member registered successfully.');
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit($id)
    {
        $member = User::findOrFail($id);
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, $id)
    {
        $member = User::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$member->id],
            'phone' => ['required', 'string', 'max:15'],
            'role' => ['required', 'in:admin,treasurer,member'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $data = $request->only('name', 'email', 'phone', 'role', 'status');
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $member->update($data);

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy($id)
    {
        $member = User::findOrFail($id);
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }
}
