<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\EmbroideryStock;
use App\Models\EmbroideryStockLog;
use App\Models\CutPieceUseable;

class Production extends Model
{
    use HasFactory;

    protected $fillable =['issue_date','vendor_name','vendor_gst_no','vendor_address','consignor_name','consignor_address','consignor_gst_no','consignee_name','consignee_address','consignee_gst_no','challan_number'];

    public function products()
    {
        return $this->morphToMany(Product::class, 'workable')->withPivot('issued_quantity');
    }
    
    public function embroidery_stocks(){
        return $this->hasMany(EmbroideryStock::class);
    }
    public function embroidery_stock_logs(){
        return $this->hasMany(EmbroideryStockLog::class);
    }

    public function cut_piece_use(){
        return $this->morphToMany(Product::class,'cut_piece_useable')->withPivot('used_pieces');
    }
    


    
}
