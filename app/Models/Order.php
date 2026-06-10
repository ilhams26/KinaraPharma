<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $order_code
 * @property string $metode_pembayaran
 * @property string $total_harga
 * @property string $status
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 */
class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_code',
        'metode_pembayaran',
        'total_harga',
        'status',
        'payment_status',
        'completed_at',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending()
    {
        return $this->status === 'diproses';
    }
    public function isReady()
    {
        return $this->status === 'siap_diambil';
    }
    public function isCompleted()
    {
        return $this->status === 'selesai';
    }
    public function isCancelled()
    {
        return $this->status === 'dibatalkan';
    }
    public function canBeCancelled()
    {
        return $this->isPending();
    }
}
