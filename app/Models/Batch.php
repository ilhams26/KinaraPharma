<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;
    protected $table = 'batches';

    protected $fillable = [
        'obat_id',
        'batch_number',
        'expired_date',
        'jumlah_awal',
        'jumlah_sisa',
    ];

    protected $casts = ['expired_date' => 'date'];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function isExpired()
    {
        return Carbon::parse($this->expired_date)->isPast();
    }
    public function isAkanExpired($days = 30)
    {
        $checkDate = Carbon::now()->addDays($days);
        return Carbon::parse($this->expired_date)->lte($checkDate);
    }
    public function reduceStock($qty)
    {
        $this->jumlah_sisa -= $qty;
        $this->save();
    }
}
