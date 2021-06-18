<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FinalStock;
use App\Models\Product;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable =['shipment_date','company_name','shipment_id','note'];

    public function final_stocks()
    {
        return $this->morphToMany(FinalStock::class, 'workable')->withPivot('issued_quantity');
    }

    public function cut_piece_use(){
        return $this->morphToMany(Product::class,'cut_piece_useable')->withPivot('used_pieces');
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'workable')->withPivot('issued_quantity','embroidery_stock_id');
    }


}
