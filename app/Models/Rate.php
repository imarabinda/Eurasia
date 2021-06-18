<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'fabric_type_id','fabric_color_id','size_id','rate'
    ];

    public function fabric_type(){
        return $this->belongsTo('\App\Models\FabricType');
    }
    public function fabric_color(){
        return $this->belongsTo('\App\Models\FabricColor');
    }

    public function size(){
        return $this->belongsTo('\App\Models\Size');
    }
    
    public function getFabricTypeNameAttribute() { 
    return $this->fabric_type_id ? $this->fabric_type->name : 'Any Type';
    }

    public function getFabricColorNameAttribute() { 
    return $this->fabric_color_id ? $this->fabric_color->name : 'Any Color';
    }

    
    public function getSizeHeightWidthAttribute() { 
    return $this->size_id ? $this->size->height. ' x '.$this->size->width : 'Any Size';
    }
    

}
