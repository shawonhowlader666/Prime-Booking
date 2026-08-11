<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'amount',
        'payment_method',
        'account_details',
        'status',
        'transaction_reference',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
