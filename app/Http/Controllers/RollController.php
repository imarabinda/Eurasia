<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fabric;
use App\Models\RollQuantityLog;
use App\Models\RollSizeLog;
use App\Models\FabricRoll;
use App\Models\Size;
use App\Models\CutPiece;

use Illuminate\Support\Facades\DB;
class RollController extends Controller
{
    public function index(Fabric $fabric){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('roll-manage')){
            abort(403);
        }
        $rolls = $fabric->id;
        $sizes = Size::get();
        
        $title = 'Manage rolls - '.$fabric->mill_id.' | '.$fabric->mill_ref_id;
        return view('rolls.index',compact('title','rolls','sizes','fabric'));
    }


    public function list(){
         
        $auth_user = auth()->user();
        if(!$auth_user->can('roll-manage')){
            abort(403);
        }

        $model = FabricRoll::where('fabric_id',request()->input('id'));
        $model->leftJoin('fabric_roll_used_quantity_logs','fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')->groupBy('fabric_rolls.id')->select(['fabric_rolls.*',DB::raw('IFNULL(sum(fabric_roll_used_quantity_logs.quantity),0) as used_quantity'),DB::raw('IFNULL(fabric_rolls.quantity - IFNULL(sum(fabric_roll_used_quantity_logs.quantity),0),0) as remaining_quantity')]);
        
        return datatables()
        ->eloquent($model)
            ->addColumn('options',function(FabricRoll $roll)use($auth_user){
            $action ='';
            if($auth_user->can('roll-add-use')){
            if($roll->used_quantity != $roll->quantity){
 
                $action = '<div class="btn-group">
                            <button class="btn btn-danger btn-add-roll-use" tabindex="0" aria-controls="rolls-data-table" data-id="'.request()->input('id').'" type="button"><span>Add Used Quantity</span></button>
                    </div>';
              }else{
                  
                $action = '<div class="btn-group">
                            <a href="javascript:void(0)" class="btn btn-success" tabindex="0" aria-controls="rolls-data-table" type="button"><span>Totally used</span></a>
                    </div>';
              }
            }

                return $action;
            
            })
            
            ->filterColumn('used_quantity', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
               $query->from('fabric_roll_used_quantity_logs')->whereColumn('fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')->havingRaw("IFNULL(SUM(fabric_roll_used_quantity_logs.quantity),0) like ?", ["%{$keyword}%"]);
           });
            })
            
            ->filterColumn('remaining_quantity', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
               $query->from('fabric_roll_used_quantity_logs')->whereColumn('fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')->havingRaw("IFNULL(fabric_rolls.quantity - SUM(fabric_roll_used_quantity_logs.quantity),0) like ?", ["%{$keyword}%"]);
           });
            })
        
        
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })->rawColumns(['options'])->toJson();
    }


    public function update(Request $request){

        $auth_user = auth()->user();
        if(!$auth_user->can('roll-add-use')){
            abort(403);
        }
    
        $request->validate([
            'id'=>'required|exists:fabric_rolls,id',
            'used_quantity'=>'required|min:1',
            'sizes'=>'required|array|min:1',
        ]);


        $data = [
            'fabric_roll_id'=>$request->id,
            'quantity'=>$request->used_quantity
        ];
        
        $roll = FabricRoll::with('fabric')->withSum('quantity_used_logs','quantity')->find($data['fabric_roll_id']);
         
        if(!$roll){
                $response['success']=false;
                $response['message']="Invalid roll provided.";
                return response()->json($response);
         }

        $addition = $roll->quantity_used_logs_sum_quantity + $data['quantity'];
        $response =array(
            'success'=>true,
        );

        if($roll->quantity >= $addition){ 
            $fabric = $roll->fabric;
            $quantity = RollQuantityLog::create($data);

            //cut pieces            
            $cut_pieces = array();
            foreach($request->sizes as $id => $val){
                if(!array_key_exists($val,$cut_pieces)){
                    $cut_pieces[$val] = (int)$request->cut_pieces[$id];
                }else{
                    $cut_pieces[$val] += $request->cut_pieces[$id];
                }
            }

            
            foreach($cut_pieces as $size => $cut_piece){

                $roll_sizes[] = new RollSizeLog(['size_id'=>$size,'pieces'=>$cut_piece,'fabric_roll_used_quantity_log_id'=>$quantity->id]);
                
                $import = CutPiece::firstOrCreate(
                    ['fabric_type_id'=>$fabric->fabric_type_id,'fabric_color_id'=>$fabric->fabric_color_id,'size_id'=>$size],
                    ['fabric_type_id'=>$fabric->fabric_type_id,'fabric_color_id'=>$fabric->fabric_color_id,'size_id'=>$size,'pieces'=>$cut_piece]
                );
                
                if(!$import->wasRecentlyCreated){
                    $import->increment('pieces',$cut_piece);
                }
                
            }

            $roll->size_used_log()->saveMany($roll_sizes);
            $response['message']= 'Used Quantity: '.$data['quantity'].', Quantity Left : '.($roll->quantity - $addition);
        }else{
            $response['success']=false;
            $response['message']="Total entered used quantity can't ". $addition." , cause exceeds Maximum quantity ".$roll->quantity;
        }
        return response()->json($response);
    }


    
    public function history(){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('roll-manage-history')){
            abort(403);
        }
        $model = RollQuantityLog::where('fabric_roll_id',request()->input('roll_id'));
        return datatables()
        ->eloquent($model)
        ->toJson();
    }

    public function quantity_history(){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('roll-manage-history')){
            abort(403);
        }
        $model = RollSizeLog::where('fabric_roll_used_quantity_log_id',request()->input('quantity_id'))->with('size');
        return datatables()
        ->eloquent($model)
        ->addColumn('size',function(RollSizeLog $roll_size_log){
            return $roll_size_log->size->height .' x '.$roll_size_log->size->width ;
        })
        ->editColumn('created_at',function(RollSizeLog $roll_size_log){
            return date('l jS \of F Y h:i:s A',strtotime($roll_size_log->created_at));
        })
        ->toJson();
    }

}
