<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $obat_id
 * @property string $foto_resep
 * @property string $status
 * @property int|null $validated_by
 * @property \Illuminate\Support\Carbon|null $validated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Obat $obat
 * @property-read \App\Models\User $validator
 */
class Prescription extends Model
{
    use HasFactory;
    protected $table = 'prescriptions';

    protected $fillable = [
        'user_id',
        'obat_id',
        'foto_resep',
        'status',
        'validated_by',
        'validated_at',
    ];

    protected $casts = ['validated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isValidated()
    {
        return $this->status === 'tervalidasi';
    }
    public function isPending()
    {
        return $this->status === 'menunggu';
    }
}
