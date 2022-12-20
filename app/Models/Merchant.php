<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchantID',
        'userID',
        'merchantBio',
        'merchantName',
        'created_at',
        'updated_at'
    ];

    protected $table = 'merchant';

    protected $primaryKey = 'merchantID';
}
