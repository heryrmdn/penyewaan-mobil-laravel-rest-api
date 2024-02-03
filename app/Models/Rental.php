<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'car_id',
        'user_id',
        'jumlah_hari_penyewaan',
        'jumlah_biaya_sewa',
        'is_active',
    ];
}
