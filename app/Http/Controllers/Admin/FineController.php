<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.fines.index', compact('fines'));
    }

    public function markAsPaid(Fine $fine)
    {
        $fine->update([
            'status' => 'paid',
            'paid_at' => Carbon::now()
        ]);
        return redirect()->back()->with('success', 'Fine marked as paid.');
    }

    public function waive(Request $request, Fine $fine)
    {
        $request->validate(['waived_reason' => 'required|string|max:255']);
        
        $fine->update([
            'status' => 'waived',
            'waived_at' => Carbon::now(),
            'waived_reason' => $request->waived_reason
        ]);
        
        return redirect()->back()->with('success', 'Fine has been waived.');
    }
}
