<?php

namespace App\Models\PaymentGateway;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMidtrans extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'transaction_time',
        'transaction_status',
        'transaction_id',
        'status_message',
        'status_code',
        'signature_key',
        'settlement_time',
        'payment_type',
        'order_id',
        'merchant_id',
        'gross_amount',
        'fraud_status',
        'currency',
        'token'
    ];

    protected $table = 'payment_midtrans';

    protected $primaryKey = 'id';
}
