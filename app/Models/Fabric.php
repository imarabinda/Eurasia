<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    use HasFactory;


    protected $fillable = ['mill_id','receiving_date','mill_ref_id','fabric_color_id','fabric_type_id','total_quantity','width'];
    
    protected $hidden =['fabric_type_id','fabric_color_id'];

    public function fabric_color()
    {
    	return $this->belongsTo('App\Models\FabricColor');
    }

    public function fabric_type()
    {
    	return $this->belongsTo('App\Models\FabricType');
    }

    public function fabric_rolls()
    {
    	return $this->hasMany('App\Models\FabricRoll');
    }

    public function used_sizes(){
        return $this->hasManyThrough(RollSizeLog::class,FabricRoll::class);
    }


    public function quantity_used(){
        return $this->hasManyThrough(RollQuantityLog::class,FabricRoll::class);
    }
    

    public function getFabricTypeNameAttribute() { 
    return $this->fabric_type_id ? $this->fabric_type->name : 'NA';
    }

    public function getFabricColorNameAttribute() { 
    return $this->fabric_color_id ? $this->fabric_color->name : 'NA';
    }
    


    public function getUsedQuantityAttribute() { 
        return $this->quantity_used->sum('quantity');
    }
    

    public function getRemainingQuantityAttribute() { 
        return $this->attributes['total_quantity'] - $this->used_quantity;
    }
    

    public function getRollsCountAttribute(){
        return $this->fabric_rolls->count();
    }

}
