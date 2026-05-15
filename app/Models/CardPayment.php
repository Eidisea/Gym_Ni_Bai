<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'card_payment_id';

    protected $fillable = [
        'transaction_id',
        'card_last_four',
        'card_type',
        'authorization_code',
        'processor_reference',
    ];

    // Constants for card types
    const TYPE_VISA = 'visa';
    const TYPE_MASTERCARD = 'mastercard';
    const TYPE_AMEX = 'amex';
    const TYPE_DISCOVER = 'discover';

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id', 'transaction_id');
    }

    // Helper methods
    public function getMaskedCardNumberAttribute(): string
    {
        return "**** **** **** {$this->card_last_four}";
    }
}
