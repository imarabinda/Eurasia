<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\FinalStock;
use App\Models\FinalStockLog;
class Stitching extends Model
{
    use HasFactory;

    protected $table = 'stitches';
    protected $fillable =['issue_date','tailor_id','consignee_name','consignee_address','consignee_gst_no','challan_number'];

    public function embroidery_stocks()
    {
        return $this->morphToMany(EmbroideryStock::class, 'workable')->withPivot('issued_quantity');
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'workable')->withPivot('issued_quantity','embroidery_stock_id');
    }

    public function final_stocks(){
        return $this->hasMany(FinalStock::class);
    }

    public function final_stock_logs(){
        return $this->hasMany(FinalStockLog::class);
    }

    public function cut_piece_use(){
        return $this->morphToMany(Product::class,'cut_piece_useable')->withPivot('used_pieces');
    }
    
    public function tailor(){
        return $this->belongsTo('App\Models\Tailor');
    }
    
}
