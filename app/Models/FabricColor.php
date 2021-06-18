<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricColor extends Model
{
    use HasFactory;
    protected $fillable =[
        "name"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
    
    public function fabric()
    {
    	return $this->hasMany('App\Models\Fabric');
    }

}
