<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\File;
use App\Models\EmbroideryStock;
use App\Models\EmbroideryStockLog;
use App\Models\FinalStock;
use App\Models\FinalStockLog;

use Illuminate\Database\Eloquent\Builder;
class Product extends Model
{
    use HasFactory;

     protected $fillable =[
        "name", "code", "product_type_id", "product_category_id","fabric_type_id","fabric_color_id","welted_edges_color_id","size_id", "rate", "image", "description", "number_of_stitches","cad_dst","cad_emb","barcode"
    ];
    

    protected $hidden = ['product_type_id','welted_edges_color_id','fabric_color_id','fabric_type_id','size_id','product_category_id'];
    
    //product category
    public function product_category()
    {
        return $this->belongsTo('App\Models\ProductCategory','product_category_id');
    }
    
    //product type 
    public function product_type()
    {
    	return $this->belongsTo('App\Models\ProductType');
    }

    //fabric color
    public function fabric_color()
    {
    	return $this->belongsTo('App\Models\FabricColor');
    }
    //fabric type
    public function fabric_type()
    {
    	return $this->belongsTo('App\Models\FabricType');
    }

    //welted edges color
    public function welted_edges_color()
    {
    	return $this->belongsTo('App\Models\WeltedEdgesColor');
    }

    public function thread_colors(){
        return $this->belongsToMany(ThreadColor::class,'product_thread_colors','product_id','thread_color_id')->withPivot('description');
    }


    public function productions(){
            return $this->morphedByMany(Production::class, 'workable');
    }

    // public function stitches(){
    //         return $this->morphedByMany(Stitching::class, 'workable');
    // }

    public function size(){
    return $this->belongsTo('App\Models\Size');     
    }

    
     public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }

    public function embroidery_stock(){
        return $this->hasOne(EmbroideryStock::class);
    }

    public function embroidery_stock_logs(){
        
        return $this->hasMany(EmbroideryStockLog::class);
    }

    public function final_stocks(){
        return $this->hasOne(FinalStock::class);
    }

    public function final_stock_logs(){
        return $this->hasMany(FinalStock::class);
    }




    public function getProductCategoryNameAttribute() { 
    return $this->product_category_id ? $this->product_category->name : 'NA';
    }
    
    public function getProductTypeNameAttribute() { 
    return $this->product_type_id ? $this->product_type->name : 'NA';
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

    
    public function getImagesAttribute() { 
    return $this->files()->where(function(Builder $query){
                    return $query->where('file_type', 'png')
                         ->orWhere('file_type',  'jpeg')
                         ->orWhere('file_type',  'jpg');
                });
    }


    public function getWeltedEdgesColorNameAttribute(){
        return $this->welted_edges_color_id ? $this->welted_edges_color->name : 'NA';
    }

    
    public function getPsdsAttribute() { 
    return $this->files()->where(function(Builder $query){
                    return $query->where('file_type', 'psd');
                });
    }
    public function getBarcodesAttribute() { 
    return $this->files()->where(function(Builder $query){
                    return $query->where('file_type', 'btw');
                });
    }
    
    public function getEmbsAttribute() { 
    return $this->files()->where(function(Builder $query){
                    return $query->where('file_type', 'emb');
                });
    }

    public function getDstsAttribute() { 
    return $this->files()->where(function(Builder $query){
                    return $query->where('file_type', 'dst');
                });
    }
    
}
