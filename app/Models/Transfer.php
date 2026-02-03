<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transfer
 * 
 * Model untuk tabel 'tbl_transfer'.
 * Menyimpan data transaksi Transfer Online (SKN/RTGS/Online).
 */
class Transfer extends Model
{
    use HasFactory;

    protected $table = 'tbl_transfer';
    protected $primaryKey = 'id_transfer';

    protected $fillable = [
        'token',
        'nama',           // Pengirim
        'no_rek',         // Pengirim
        'tgl',
        'nominal',
        'terbilang',
        'tujuan',
        'nama_penyetor',  // Penyetor (bisa sama dengan pengirim)
        'hp_penyetor',
        'alamat_',        // Alamat Penyetor
        
        // Penerima
        'nama_tujuan',
        'no_rek_tujuan',
        'bank_tujuan',
        'berita_tujuan',
        'alamat_tujuan',
        'kota_tujuan',
        'biaya_trf',
        'jenis_trf',     // ONLINE, SKN, RTGS
        'hp_penerima',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = null;
    public $timestamps = false;
}
