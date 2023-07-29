<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbParkir extends Model
{
    use HasFactory;

    protected $table = 'tb_parkir';

    protected $fillable = [
        'unicode',
        'nopol',
        'clock_in',
        'clock_out',
        'price',
        'status',
    ];
}
