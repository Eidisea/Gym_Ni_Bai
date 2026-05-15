<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EwalletPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'ewallet_payment_id';

    protected $fillable = [
        'transaction_id',
        'provider',
        'reference_number',
        'account_identifier',
    ];

    // Constants for providers
    const PROVIDER_GCASH = 'gcash';
    const PROVIDER_PAYMAYA = 'paymaya';
    const PROVIDER_GRABPAY = 'grabpay';
    const PROVIDER_PAYPAL = 'paypal';

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id', 'transaction_id');
    }

    // Helper methods
    public function getProviderDisplayNameAttribute(): string
    {
        return match($this->provider) {
            self::PROVIDER_GCASH => 'GCash',
            self::PROVIDER_PAYMAYA => 'PayMaya',
            self::PROVIDER_GRABPAY => 'GrabPay',
            self::PROVIDER_PAYPAL => 'PayPal',
            default => ucfirst($this->provider),
        };
    }
}
