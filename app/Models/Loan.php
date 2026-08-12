<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'amount_requested',
        'amount_approved',
        'interest_rate',
        'total_repayable',
        'amount_repaid',
        'balance',
        'reason',
        'status',
        'repayment_months',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
