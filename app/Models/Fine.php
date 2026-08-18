<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'amount', 'reason', 'type', 'status', 'paid_at', 'waived_at', 'waived_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
