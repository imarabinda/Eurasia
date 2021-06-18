<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\EmbroideryStock;
use App\Model\FinalStock;

class Workable extends Model
{
    use HasFactory;

    public function embroidery_stocks(){
        return $this->belongsTo(EmbroideryStock::class);
    }
    
    public function final_stocks(){
        return $this->belongsTo(FinalStock::class);
    }

}
