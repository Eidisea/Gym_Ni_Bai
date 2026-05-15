<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'customer_id',
        'subscription_id',
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'transaction_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    // Constants for payment methods
    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_EWALLET = 'ewallet';

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_VOIDED = 'voided';

    // Relationships
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id', 'customer_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(MembershipSubscription::class, 'subscription_id', 'subscription_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ClassBooking::class, 'booking_id', 'booking_id');
    }

    // Polymorphic-style payment method relationships
    public function cashPayment(): HasOne
    {
        return $this->hasOne(CashPayment::class, 'transaction_id', 'transaction_id');
    }

    public function cardPayment(): HasOne
    {
        return $this->hasOne(CardPayment::class, 'transaction_id', 'transaction_id');
    }

    public function ewalletPayment(): HasOne
    {
        return $this->hasOne(EwalletPayment::class, 'transaction_id', 'transaction_id');
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function isVoided(): bool
    {
        return $this->status === self::STATUS_VOIDED;
    }

    public function getPaymentDetailsAttribute()
    {
        return match($this->payment_method) {
            self::METHOD_CASH => $this->cashPayment,
            self::METHOD_CARD => $this->cardPayment,
            self::METHOD_EWALLET => $this->ewalletPayment,
            default => null,
        };
    }
}
