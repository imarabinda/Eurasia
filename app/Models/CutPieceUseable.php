<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CutPiece;

class CutPieceUseable extends Model
{
    use HasFactory;

    protected $fillable = ['used_pieces','cut_piece_id','product_id','production_id'];

    public function cut_piece(){
        return $this->belongsTo(CutPiece::class);
    }

    public function cut_piece_useable(){
        return $this->morphTo();
    }

}
