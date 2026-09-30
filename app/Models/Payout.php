<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'vendor_name',
        'amount',
        'payment_method',
        'account_details',
        'account_number',
        'reference_number',
        'status',
        'notes',
        'processed_by',
        'processed_at',
        'fee_deducted',
        'requested_at',
        'payout_date',
        'created_at',
        'updated_at',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
