<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'product',
        'position',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}