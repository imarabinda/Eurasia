<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;
use App\Models\Fabric;
use App\Models\FabricRoll;
use Illuminate\Support\Facades\DB;

class FabricController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-manage')){
            abort(403);
        }

        $sizes = Size::get();
        
       
        $title = 'Manage Fabrics';
        return view('fabrics.index',compact('title','sizes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth_user = auth()->user();
        
        if(!$auth_user->can('fabric-create')){
            abort(403);
        }

        $fabric_types = FabricType::with('colors')->get();
       
        $title = 'Add New Fabric';
        return view('fabrics.create',compact('fabric_types','title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-create')){
            abort(403);
        }
        
        
        $request->validate([
            'receiving_date'=>'required',
            'mill_id'=>'required',
            'mill_ref_id'=>'required',
            
            'width'=>'required|integer|min:1',
            'total_quantity'=>'required|integer|min:1',
            
            'fabric_types'=>'required|exists:fabric_types,id',
            'fabric_colors'=>'required_if:fabric_types,exists:fabric_type_colors,fabric_type_id|exists:fabric_colors,id',
            'roll_quantity'=>'required|array|min:1',
            'roll_name'=>'required|array|min:1',
            
        ]);
        
        $data = $request->except('rolls','roll_quantity','fabric_colors','fabric_types');
        
        $data['total_quantity']=(int)$data['total_quantity'];
        $data['width']=(int)$data['width'];
        $data['fabric_color_id']=$request->fabric_colors;
        $data['fabric_type_id']=$request->fabric_types;
        $data['remaining_quantity']= (int)$data['total_quantity'];
        
        $fabric = Fabric::create($data);
 
        $rolls = [];

        $total=0;
        foreach($request->roll_quantity as $id=>$quantity){
            if($quantity <= 0 ){
                continue;
            }
            $rolls[] = new FabricRoll(['quantity'=>$quantity,'name'=>$request->roll_name[$id]]);
            $total = $quantity + $total;
        }
        
        if($data['total_quantity'] != $total){
            return false;
        }

        $fabric->fabric_rolls()->saveMany($rolls);

        
        return response()->json(
            array(
                'success'=>true,
            'redirect'=>route('fabrics.index')
        ));
     
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Fabric $fabric)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-view')){
            abort(403);
        }

        $title = 'View Fabric - '.$fabric->mill_id.'| '.$fabric->mill_ref_id;
        return view('fabrics.show',compact('fabric','title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Fabric $fabric)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-edit')){
            abort(403);
        }

        // $fabric = Fabric::withSum('quantity_used','quantity')->find($id);
        $fabric->loadCount('quantity_used');
 
        $fabric_types = FabricType::get();
        
        $title = 'Edit Fabric - '.$fabric->mill_id.'| '.$fabric->mill_ref_id;
        return view('fabrics.edit',compact('fabric','fabric_types','title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-edit')){
            abort(403);
        }

         $request->validate([
            'receiving_date'=>'required',
            'mill_id'=>'required',
            'mill_ref_id'=>'required',

            'width'=>'required|integer|min:1',
            
            'total_quantity'=>'required|integer|min:1',
            
            'fabric_types'=>'required|exists:fabric_type,id',
            'fabric_colors'=>'required_if:fabric_types,exists:fabric_type_colors,fabric_type_id|exists:fabric_colors,id',
            
        ]);

        $data = $request->except('rolls','roll_quantity','fabric_colors','fabric_types','total_quantity');

        $data['width']=(int)$data['width'];
        
        $response = array(
            'success'=>true,
            'redirect'=>route('fabrics.index')
        );

        $fabric = Fabric::with('fabric_rolls')->withSum('fabric_rolls','quantity')->withSum('quantity_used','quantity')->find($id);
        
        $total = $fabric->fabric_rolls_sum_quantity;
        $roll_names = $request->roll_name;

        if($fabric->quantity_used_sum_quantity <= 0 ){
            
            if($request->has('fabric_types')){
                $data['fabric_type_id']=$request->fabric_types;
            }
            
            if($request->has('fabric_colors')){
                $data['fabric_color_id']=$request->fabric_colors;
            }

            $new_rolls=[];
            if($request->has('roll_quantity') && count($request->roll_quantity) > 0 ){
                    $total = 0;
                    foreach($request->roll_quantity as $key=>$quantity){
                            if($quantity <= 0 ){
                                continue;
                            }
                            $new_rolls[] = new FabricRoll(['quantity'=>$quantity,'name'=>$roll_names[$key]]);
                            $total = $quantity + $total;
                        }
                    
            }
            
        
                if(count($new_rolls) > 0 && $total == $request->total_quantity){
                    $fabric->fabric_rolls()->delete();
                    $fabric->fabric_rolls()->saveMany($new_rolls);
                    $data['total_quantity']=(int)$request->total_quantity;

                }else{
                    $response['message'] = 'Quantity not matched.';  
                    $response['success'] = false;  
                    return response()->json($response);
                }

        }else{

            $rolls = [];
        

            $rolls = $fabric->fabric_rolls;
            $roll_quantities = $request->roll_quantity;

            $ids = $rolls->pluck('name','id');
            $new_rolls=[];
            if($request->has('roll_quantity') && count($request->roll_quantity) > 0 ){
                    $create_ids = array_diff_key($request->roll_quantity,$ids->toArray());
                    
                    $last =array_key_last($ids->toArray());
                    if(is_array($create_ids) && count($create_ids) > 0){
                        foreach($create_ids as $key=>$quantity){
                            if($quantity <= 0 ){
                                continue;
                            }
                            $new_rolls[] = new FabricRoll(['quantity'=>$quantity,'name'=>$roll_names[$last+1+$key]]);
                            $total = $quantity + $total;
                        }
                    }
            }



        
            if($total == $request->total_quantity){
                    
                if(count($new_rolls) > 0){
                    $fabric->fabric_rolls()->saveMany($new_rolls);
                    $data['total_quantity']=(int)$request->total_quantity;
            
                }
            }else{
                    $response['message'] = 'Quantity not matched.';     
                    $response['success'] = false;  
                    return response()->json($response);
            }
        
            $update_ids = array_intersect_key($request->roll_name,$ids->toArray());
            if(is_array($update_ids) && count($update_ids) > 0){
                $rolls=[];
                foreach($update_ids as $key=>$name){
                if(empty($name)){
                    continue;
                }
                FabricRoll::where('id','=',$key)->update(array('name'=>$name));   
                }
            }

        // $delete_ids = array_diff_key($ids->toArray(),$roll_quantities);
        // if(is_array($delete_ids) && count($delete_ids) > 0){
        //     foreach($delete_ids as $key=>$value){
        //         $roll = FabricRoll::find($key);
        //         $roll->quantity_used_logs()->delete();
        //         $sizes= $roll->size_used_log();
        //         foreach($sizes as $size_id=>$val){
        //             $cut_piece = CutPiece::where([['fabric_type_id'=>$fabric->fabric_type_id,'fabric_color_id'=>$fabric->fabric_color_id,'size_id'=>$size_id]])->get();
        //             $cut_piece->decrement('pieces',$val);
        //         }
        //         $sizes->delete();
        //         $roll->delete();
        //     }
        // }
    }
    
        $fabric->update($data);
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fabric $fabric)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-delete')){
            abort(403);
        }

        if($fabric->quantity_used->count() <= 0){
            $fabric->delete();
        }
        return redirect('fabrics')->with('message', 'Fabric deleted successfully');
    }


    public function list(){

        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-manage')){
            abort(403);
        }

        $model = Fabric::with(['fabric_type:id,name','fabric_color:id,name']);
        $model->leftJoin('fabric_rolls',function($join){
          $join->on('fabric_rolls.fabric_id','=','fabrics.id');
        })->groupBy("fabrics.id")
        ->select('fabrics.*')
        ->leftJoin('fabric_roll_used_quantity_logs','fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')
        ->selectRaw("IFNULL(SUM(fabric_roll_used_quantity_logs.quantity),0) as used_quantity")->selectRaw("IFNULL(fabrics.total_quantity - IFNULL(SUM(fabric_roll_used_quantity_logs.quantity),0),0) as remaining_quantity");
        
        return datatables()
        ->eloquent($model)
            ->addColumn('options',function(Fabric $fabric)use($auth_user){
                $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';

if($auth_user->can('fabric-view')){
    $action .='<a class="dropdown-item" href="'.route('fabrics.show',$fabric->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
}

if($auth_user->can('fabric-edit')){
                $action .='<a class="dropdown-item" href="'.route('fabrics.edit',$fabric->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
}
                if($auth_user->can('roll-manage') && $fabric->rolls_count > 0){
                    $action .='<a class="dropdown-item" href="'.route('rolls.index',$fabric->id).'"><i class="fa fa-edit"></i> '.trans('Edit Rolls').'</a>';
                }
                
                $sum =$fabric->used_quantity;
                if(is_null($sum) || $sum <= 0){
                if($auth_user->can('fabric-delete')){
                    
                
                $action .=
                
                                    \Form::open(["route" => ["fabrics.destroy", $fabric->id], "method" => "DELETE"] ).'
                                          <button type="submit" class="dropdown-item btn btn-link delete-button" ><i class="fa fa-trash"></i> '.trans("Delete").'</button> 
                                        '.\Form::close();
                }               
            }       

                $action .=     '</div>
                                            </div>';
                return $action;
            
            })
            // ->editColumn('mill_ref_id',function(Fabric $fabric)use ($auth_user){
                
            //     $content = $fabric->mill_ref_id;
            //     if($auth_user->can('fabric-view')){
            //       $content = '<a href="'.route('fabric.show',$fabric->id).' target="_blank">'.$content.'</a>';  
            //     }
            //     return $content;
            // })
            // ->editColumn('mill_id',function(Fabric $fabric) use ($auth_user){
                
            //     $content = $fabric->mill_id;
            //     if($auth_user->can('fabric-view')){
            //       $content = '<a href="'.route('fabric.show',$fabric->id).' target="_blank">'.$content.'</a>';  
            //     }
            //     return $content;
            // })
          
        ->filterColumn('remaining_quantity', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('fabric_rolls')->join('fabric_roll_used_quantity_logs','fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')->whereColumn('fabrics.id','=','fabric_rolls.fabric_id')->havingRaw("IFNULL(fabrics.total_quantity - IFNULL(SUM(fabric_roll_used_quantity_logs.quantity),0),0) like ?", ["%{$keyword}%"]);
           });
        })
        ->filterColumn('used_quantity', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('fabric_rolls')->join('fabric_roll_used_quantity_logs','fabric_rolls.id','=','fabric_roll_used_quantity_logs.fabric_roll_id')->whereColumn('fabrics.id','=','fabric_rolls.fabric_id')->havingRaw("IFNULL(SUM(fabric_roll_used_quantity_logs.quantity),0) like ?", ["%{$keyword}%"]);
           });
        })
        ->orderColumn('receiving_date', function ($query, $order) {
                     $query->orderBy('receiving_date', $order)->orderBy('created_at',$order);
                 })
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })->rawColumns(['options','mill_id','mill_ref_id'])->toJson();
    }

    public function delete_by_selection(Request $request){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('fabric-delete')){
            abort(403);
        }

        $ids = $request->ids;
        foreach ($ids as $id) {
            $data = Fabric::findOrFail($id);

            $data->delete();
        }
        return 'Fabric deleted successfully!';
    }



   


}
