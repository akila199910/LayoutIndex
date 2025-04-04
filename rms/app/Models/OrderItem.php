<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'order_id',
    ];

    public function concession_info(){
        return $this->hasOne(Concession::class, 'id', 'concession_id');
    }
}
