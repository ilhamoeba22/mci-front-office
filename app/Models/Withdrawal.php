<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Withdrawal
 * 
 * Model untuk tabel 'tbl_tarik'.
 * Menyimpan data transaksi Penarikan Tunai.
 */
class Withdrawal extends Model
{
    use HasFactory;

    protected $table = 'tbl_tarik';
    protected $primaryKey = 'id_tarik'; // Perhatikan PK-nya bukan 'id' standard

    protected $fillable = [
        'token',
        'nama',
        'no_rek',
        'tgl',
        'nominal',
        'terbilang',
        'jenis_rekening',
        'tujuan',
        'nama_penarik',
        'hp_penarik',
        'noid_penarik',
        'alamat_penarik',
        'created',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = null;
    public $timestamps = false;
}
