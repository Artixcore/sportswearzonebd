<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'guest_email',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'currency',
        'payment_method',
        'source',
        'meta',
        'shipping_name',
        'shipping_phone',
        'shipping_city',
        'shipping_address',
        'billing_name',
        'billing_phone',
        'billing_address',
        'created_by',
        'updated_by',
        'delivery_charge',
        'delivery_advance_paid',
        'delivery_advance_method',
        'delivery_advance_txn_id',
        'delivery_advance_customer_confirmed',
        'delivery_advance_admin_txn_id',
        'delivery_advance_admin_verified',
        'delivery_settlement_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'meta' => 'array',
            'delivery_charge' => 'decimal:2',
            'delivery_advance_paid' => 'decimal:2',
            'delivery_advance_customer_confirmed' => 'boolean',
            'delivery_advance_admin_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
