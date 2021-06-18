<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricRoll extends Model
{
    use HasFactory;

    protected $fillable = ['quantity','name'];
    
    public function fabric(){
	    return $this->belongsTo(Fabric::class);
    }  
    
    public function quantity_used_logs(){
	    return $this->hasMany(RollQuantityLog::class);
    }

    public function size_used_log(){
	    return $this->hasMany(RollSizeLog::class);
    }
    
}
