<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CutPiece;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;

use Illuminate\Support\Facades\DB;
class CutPieceController extends Controller
{
    public function index(){
        $auth_user = auth()->user();
        if(!$auth_user->can('cut-piece-manage')){
            abort(403);
        }
        $fabric_types = FabricType::get();
        $fabric_colors = FabricColor::get();
        $sizes = Size::get();
        
        return view('cut_pieces.index',compact('fabric_types','fabric_colors','sizes'));
    }

    public function list(){

        $auth_user = auth()->user();
        if(!$auth_user->can('cut-piece-manage')){
            abort(403);
        }
        
        $fabric_type_id = request()->fabric_type;
        $fabric_color_id = request()->fabric_color;
        $size_id = request()->size; 
        
        $model = CutPiece::when($fabric_type_id,function ($query, $fabric_type_id) {
                    return $query->where('fabric_type_id', $fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->with(['fabric_type:id,name','fabric_color:id,name','size:id,width,height'])->select('cut_pieces.*');
 
        $model->leftJoin('cut_piece_useables','cut_pieces.id','=','cut_piece_useables.cut_piece_id')->groupBy('cut_pieces.id')->select(['cut_pieces.*',DB::raw('IFNULL(sum(cut_piece_useables.used_pieces),0) as used_pieces'),DB::raw('IFNULL(cut_pieces.pieces - IFNULL(sum(cut_piece_useables.used_pieces),0),0) as remaining_pieces')]);
        return datatables()
        ->eloquent($model)
            
            ->addColumn('fabric_type', function(CutPiece $cut_piece){
                return  $cut_piece->fabric_type_name;
            })
            ->addColumn('size', function(CutPiece $cut_piece){
                return  $cut_piece->size_height_width;
            })
            
        ->filterColumn('size', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
               $query->from('sizes')->whereRaw("sizes.id = cut_pieces.size_id AND CONCAT(sizes.height,' x ',sizes.width) like ?", ["%{$keyword}%"]);
           });
            })
            
        ->filterColumn('remaining_pieces', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('cut_piece_uses')->whereColumn('cut_pieces.id','=','cut_piece_uses.cut_piece_id')->havingRaw("IFNULL(cut_pieces.pieces - IFNULL(SUM(cut_piece_uses.used_pieces),0),0) like ?", ["%{$keyword}%"]);
           });
            })
        ->filterColumn('used_pieces', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('cut_piece_uses')->whereColumn('cut_pieces.id','=','cut_piece_uses.cut_piece_id')->havingRaw("IFNULL(SUM(cut_piece_uses.used_pieces),0) like ?", ["%{$keyword}%"]);
           });
            })

            ->addColumn('fabric_color', function(CutPiece $cut_piece){
                return  $cut_piece->fabric_color_name;
        })  
        ->toJson();    
    }



    
  
}
