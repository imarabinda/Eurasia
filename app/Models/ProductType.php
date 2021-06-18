<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{

    use HasFactory;

    protected $fillable =[
        "name"
    ];

     
    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }

    
    public function fabric_types(){
        return $this->belongsToMany(FabricType::class, 'product_fabric_types','product_type_id','fabric_type_id');
    }

    
    public function categories(){
        return $this->belongsToMany(ProductCategory::class, 'product_category_types');
    }
}
