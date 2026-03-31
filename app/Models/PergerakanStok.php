<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PergerakanStok extends Model
{
    use HasFactory;
    protected $table = 'pergerakan_stok';

    protected $fillable = [
        'obat_id',
        'tipe',
        'jumlah',
        'sumber',
        'referensi_id',
        'keterangan',
        'created_by',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
