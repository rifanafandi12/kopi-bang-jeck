<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode', 'cafe_table_id', 'nama', 'telepon', 'email',
        'status', 'metode_bayar', 'status_bayar',
        'subtotal', 'total', 'catatan',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'metode_bayar' => PaymentMethod::class,
        'status_bayar' => PaymentStatus::class,
        'subtotal' => 'integer',
        'total' => 'integer',
    ];

    public function cafeTable(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('occurred_at');
    }
}
