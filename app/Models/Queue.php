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

    // Ensure transaction details are sent to JSON
    protected $appends = ['transaction', 'tx_type'];

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * 
     * @var array
     */
    protected $fillable = [
        'tgl_antri',
        'nama_antrian',
        'type', // Tipe antrian: 'CS' atau 'Teller'
        'antrian', // Nomor antrian string (Contoh: CS-01)
        'kode', // Token unik
        'st_antrian', // Status antrian (0=Menunggu, 1=Dipanggil, dst)
        'tujuan_datang',
        'solusi',
        'nama',
        'no_kontak',
        'waktu_panggil',
    ];

    /**
     * Menentukan kolom timestamp kustom.
     * Menggunakan 'created' sebagai pengganti created_at default jika diperlukan.
     */
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated_at';

    /**
     * Relationship: Setor Tunai (Transaction)
     */
    public function setor()
    {
        return $this->hasOne(\App\Models\Transaction::class , 'token', 'kode');
    }

    /**
     * Relationship: Tarik Tunai (Withdrawal)
     */
    public function tarik()
    {
        return $this->hasOne(\App\Models\Withdrawal::class , 'token', 'kode');
    }

    /**
     * Relationship: Transfer Only (Online)
     */
    public function transfer()
    {
        return $this->hasOne(\App\Models\Transfer::class , 'token', 'kode');
    }

    /**
     * Accessor untuk mendapatkan data transaksi terkait.
     * Menggunakan logika prefix pada kolom 'kode'.
     * 
     * Usage: $queue->transaction
     */
    public function getTransactionAttribute()
    {
        if (!$this->kode)
            return null;

        if (str_starts_with($this->kode, 'ST-')) {
            return \App\Models\Transaction::where('token', $this->kode)->first();
        }
        elseif (str_starts_with($this->kode, 'TT-')) {
            return \App\Models\Withdrawal::where('token', $this->kode)->first();
        }
        elseif (str_starts_with($this->kode, 'ON-')) {
            return \App\Models\Transfer::where('token', $this->kode)->first();
        }

        return null;
    }

    /**
     * Accessor untuk mendapatkan jenis transaksi dalam teks.
     */
    public function getTxTypeAttribute()
    {
        if (!$this->kode)
            return 'General';

        if (str_starts_with($this->kode, 'ST-'))
            return 'Setor Tunai';
        if (str_starts_with($this->kode, 'TT-'))
            return 'Tarik Tunai';
        if (str_starts_with($this->kode, 'ON-'))
            return 'Transfer';

        return 'Layanan CS';
    }
}
