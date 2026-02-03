<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Queue
 * 
 * Model Eloquent untuk tabel 'tbl_antrian'.
 * Menyimpan data antrian harian untuk CS dan Teller.
 */
class Queue extends Model
{
    use HasFactory;

    protected $table = 'tbl_antrian';
    protected $primaryKey = 'id_antrian';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * 
     * @var array
     */
    protected $fillable = [
        'tgl_antri',
        'nama_antrian',
        'type',         // Tipe antrian: 'CS' atau 'Teller'
        'antrian',      // Nomor antrian string (Contoh: CS-01)
        'kode',         // Token unik
        'st_antrian',   // Status antrian (0=Menunggu, 1=Dipanggil, dst)
        'tujuan_datang',
        'solusi',
    ];

    /**
     * Menentukan kolom timestamp kustom.
     * Menggunakan 'created' sebagai pengganti created_at default jika diperlukan.
     */
    const CREATED_AT = 'created';
    const UPDATED_AT = null; // Disable updated_at
    public $timestamps = false; // Disable auto timestamps completely to be safe with legacy schema
}
