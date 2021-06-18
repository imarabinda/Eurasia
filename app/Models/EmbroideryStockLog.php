<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmbroideryStockLog extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','production_id','received_embroidery','received_damage'];

    public function product(){
        return $this->belongsTo('\App\Models\Product');
    }

    public function production(){
        return $this->belongsTo('\App\Models\Production');
    }
    
}
