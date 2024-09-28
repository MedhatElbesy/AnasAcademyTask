<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

        public $guarded = [ 'created_at', 'updated_at'];

        public function category() {
            return $this->belongsTo(category::class);
        }

        public function orderItems() {
            return $this->hasMany(OrderItem::class);
        }

}
