<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * 
 * Model untuk tabel 'tbl_set'.
 * Menyimpan konfigurasi sistem dan media (Video/Text).
 */
class Setting extends Model
{
    use HasFactory;

    protected $table = 'tbl_set';
    protected $primaryKey = 'id_set';

    protected $fillable = [
        'jenis_set', // 'Video', 'Text'
        'value',     // Filename atau Isi Teks
    ];
}
