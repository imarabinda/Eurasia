<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shipment;
use App\Models\Workable;

class FinalStock extends Model
{
    use HasFactory;

    
    public function getUpdatedAtAttribute($date)
    {
        return date('Y-m-d H:i:s',strtotime($date));
    }

    public function getCreatedAtAttribute($date)
    {
        return date('Y-m-d H:i:s',strtotime($date));
    }
    
    protected $fillable = ['product_id','stitching_id','received_stitches','received_damage'];

    public function product(){
        return $this->belongsTo('\App\Models\Product');
    }

    public function stitching(){
        return $this->belongsTo('\App\Models\Stitching');
    }

    public function shipments(){
            return $this->morphedByMany(Shipment::class, 'workable')->withPivot('issued_quantity');
    }
    
    public function workables(){
        return $this->hasMany(Workable::class);
    }
    
}
