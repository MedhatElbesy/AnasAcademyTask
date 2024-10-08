<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
        public $guarded = ['created_at', 'updated_at'];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
