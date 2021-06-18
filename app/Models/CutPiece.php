<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CutPieceUseable;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;


class CutPiece extends Model
{
    use HasFactory;

    protected $fillable = ['pieces','fabric_type_id','fabric_color_id','size_id'];
 

    public function used_pieces(){
        return $this->hasMany(CutPieceUseable::class);
    }
    
    public function fabric_type(){
        return $this->belongsTo(FabricType::class);
    }

    public function fabric_color(){
        return $this->belongsTo(FabricColor::class);
    }

    public function size(){
        return $this->belongsTo(Size::class);
        
    }

    
    public function getFabricTypeNameAttribute() {
    return $this->fabric_type_id ? $this->fabric_type->name : 'NA';
    }

    public function getFabricColorNameAttribute() { 
    return $this->fabric_color_id ? $this->fabric_color->name : 'NA';
    }

    public function getSizeHeightWidthAttribute() { 
    return $this->size_id ? $this->size->height. ' x '.$this->size->width : 'NA';
    }
}


