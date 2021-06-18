<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class File extends Model
{
    use HasFactory;

    protected $fillable = ['link','file_type'];

    public function products(){
        return $this->morphedByMany(Product::class, 'fileable');
    }

}
