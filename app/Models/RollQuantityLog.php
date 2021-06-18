<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollQuantityLog extends Model
{
    use HasFactory;
 
    protected $fillable = ['fabric_roll_id','quantity'];
    
    protected $table = 'fabric_roll_used_quantity_logs';
    
    public function roll(){
        return $this->belongsTo(FabricRoll::class);
    }

    public function getCreatedAtAttribute($date){
        return date('l jS \of F Y h:i:s A',strtotime($date));
    }
    public function getUpdatedAtAttribute($date){
        return date('l jS \of F Y h:i:s A',strtotime($date));
    }
    
    public function sizes(){
	    return $this->hasMany(RollSizeLog::class);
    }
    
}
