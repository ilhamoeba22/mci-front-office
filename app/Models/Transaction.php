<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * 
 * Model Eloquent untuk tabel 'tbl_setor'.
 * Menyimpan data transaksi perbankan seperti Setor Tunai.
 */
class Transaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_setor';
    protected $primaryKey = 'id_setor';

    /**
     * Atribut yang dapat diisi secara massal.
     * 
     * @var array
     */
    protected $fillable = [
        'token',            // Token unik transaksi
        'nama',             // Nama pemilik rekening
        'no_rek',           // Nomor rekening tujuan
        'tgl',              // Tanggal transaksi
        'nominal',          // Jumlah nominal (angka)
        'terbilang',        // Jumlah nominal (teks)
        'berita',           // Keterangan tambahan
        'tujuan',           // Tujuan transaksi
        'nama_penyetor',    // Nama penyetor
        'hp_penyetor',      // Nomor HP penyetor
        'noid_penyetor',    // Nomor identitas penyetor (KTP)
        'alamat_penyetor',  // Alamat penyetor
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = null; // Disable updated_at
    public $timestamps = false; // Disable auto timestamps
}
