<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
