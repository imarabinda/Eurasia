<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    
    protected $fillable =[
        "name", 'image'
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }

    
    public function types(){
        return $this->belongsToMany(ProductType::class, 'product_category_types','product_category_id','product_type_id');
    }
    
}
