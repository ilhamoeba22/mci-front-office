<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank
 * 
 * Model untuk tabel 'tbl_bank'.
 * Menyimpan daftar master Bank dan biaya transfer.
 */
class Bank extends Model
{
    use HasFactory;

    protected $table = 'tbl_bank';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_bank',
        'biaya_trf',
    ];
}
