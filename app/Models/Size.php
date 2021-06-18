<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;
    protected $fillable =[
        "height","width"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
    
    public function fabric_used_roll()
    {
    	return $this->hasMany('App\Models\FabricUsedRoll');
    }
    
}
