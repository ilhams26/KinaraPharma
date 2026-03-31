<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;
    protected $table = 'obat';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'harga',
        'jenis',
        'stok_minimum',
        'foto',
    ];

    protected $casts = ['harga' => 'decimal:2'];
    protected $appends = ['stok_total'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
    public function batches()
    {
        return $this->hasMany(Batch::class, 'obat_id');
    }
    public function pergerakanStok()
    {
        return $this->hasMany(PergerakanStok::class, 'obat_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'obat_id');
    }
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'obat_id');
    }

    public function getStokTotalAttribute()
    {
        return $this->batches()->sum('jumlah_sisa');
    }

    public function isObatKeras()
    {
        return $this->jenis === 'keras';
    }
    public function isStokMenipis()
    {
        return $this->stok_total < $this->stok_minimum;
    }
    public function isStokHabis()
    {
        return $this->stok_total == 0;
    }
}
