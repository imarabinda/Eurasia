<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricType extends Model
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

    public function colors(){
        return $this->belongsToMany(FabricColor::class, 'fabric_type_colors', 'fabric_type_id', 'fabric_color_id');
    }


    

}
