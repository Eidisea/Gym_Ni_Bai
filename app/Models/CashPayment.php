<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_payment_id';

    protected $fillable = [
        'transaction_id',
        'staff_id',
        'amount_received',
        'change_given',
    ];

    protected function casts(): array
    {
        return [
            'amount_received' => 'decimal:2',
            'change_given' => 'decimal:2',
        ];
    }

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id', 'transaction_id');
    }

    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id', 'staff_id');
    }
}
