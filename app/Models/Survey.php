<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rating',
        'reference_no',
        'comment'
    ];

    /**
     * Relasi ke Staff/User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
