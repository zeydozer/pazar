<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    
    public $timestamps = false;
    
    protected $guarded = ['id'];

    protected $attributes = 
    [
        'price' => 0,
        'discount' => 0,
        'stock' => 0,
        'deci' => 0,
        'price' => 0,
        'discount' => 0,
        'tax' => 0,
        'width' => 0,
        'length' => 0,
        'height' => 0,
        'width_p' => 0,
        'length_p' => 0,
        'height_p' => 0,
        'prepare' => 1,
    ];

    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function profile()
    {
        return $this->hasOne(Photo::class, 'product_id', 'id')->where('profile', 1);
    }
}
