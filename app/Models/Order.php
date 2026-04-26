<?php

namespace App\Models;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'first_name',
        'last_name',
        'street',
        'house_number',
        'apartment_number',
        'postal_code',
        'city',
        'email',
        'status',
        'payment_method',
        'payment_status',
        'is_club_member',
        'total_shots',
        'total_amount',
        'verification_code_hash',
        'verification_code_expires_at',
        'verified_at',
        'paid_at',
        'is_completed',
        'completed_at',
        'download_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_method' => OrderPaymentMethod::class,
            'payment_status' => OrderPaymentStatus::class,
            'is_club_member' => 'boolean',
            'is_completed' => 'boolean',
            'total_amount' => 'decimal:2',
            'verification_code_expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'paid_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCustomerFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFullAddressAttribute(): string
    {
        $houseWithApartment = $this->house_number;

        if (! empty($this->apartment_number)) {
            $houseWithApartment .= '/'.$this->apartment_number;
        }

        return trim("{$this->street} {$houseWithApartment}, {$this->postal_code} {$this->city}");
    }

    public function canBeMarkedAsPaid(): bool
    {
        return $this->payment_status !== OrderPaymentStatus::Paid;
    }

    public function markAsPaid(): void
    {
        if (! $this->canBeMarkedAsPaid()) {
            return;
        }

        $this->forceFill([
            'payment_status' => OrderPaymentStatus::Paid,
            'paid_at' => now(),
        ])->save();
    }

    public function canBeCompleted(): bool
    {
        return ! $this->is_completed;
    }

    public function markAsCompleted(): void
    {
        if (! $this->canBeCompleted()) {
            return;
        }

        $this->forceFill([
            'is_completed' => true,
            'completed_at' => now(),
        ])->save();
    }

    public function paymentMethodLabel(): string
    {
        return $this->payment_method->label();
    }

    public function paymentStatusLabel(): string
    {
        return $this->payment_status->label();
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }
}
