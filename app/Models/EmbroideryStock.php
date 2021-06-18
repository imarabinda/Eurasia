<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stitching;
use App\Models\Workable;

class EmbroideryStock extends Model
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
    protected $fillable = ['product_id','production_id','received_embroidery','received_damage'];

    public function product(){
        return $this->belongsTo('\App\Models\Product');
    }

    public function workables(){
        return $this->hasMany(Workable::class);
    }
    

    public function stitches(){
        return $this->morphedByMany(Stitching::class, 'workable')->withPivot('issued_quantity');
    }

    public function production(){
        return $this->belongsTo('\App\Models\Production');
    }
    
}
