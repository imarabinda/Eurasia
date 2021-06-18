<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FabricRoll;
use App\Models\Size;
use App\Models\RollQuantityLog;

class RollSizeLog extends Model
{
    use HasFactory;
 
    protected $fillable = ['size_id','pieces','fabric_roll_id','fabric_roll_used_quantity_log_id'];
    protected $table = 'fabric_roll_used_size_logs';
    
    public function roll(){
        return $this->belongsTo(FabricRoll::class);
    }

    
    public function getCreatedAtAttribute($date){
        return date('l jS \of F Y h:i:s A',strtotime($date));
    }
    
    public function getUpdatedAtAttribute($date){
        return date('l jS \of F Y h:i:s A',strtotime($date));
    }
    
    public function quantity_used(){
        return $this->belongsTo(RollQuantityLog::class);
    }
    public function size(){
        return $this->belongsTo(Size::class);
    }
}
