<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;


class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Manage Rates';
        return view('rates.index',compact('title'));
    }


    
    public function list(){

        $auth_user = auth()->user();
        if(!$auth_user->can('rate-manage')){
            abort(403);
        }
        $platform = request()->platform;

        $model = Rate::with(['fabric_type','fabric_color','size']);
        
        return datatables()
        ->eloquent($model)
        ->addColumn('fabric_type',function(Rate $rate){
            return $rate->fabric_type_name; 
        })
        ->addColumn('fabric_color',function(Rate $rate){
            return $rate->fabric_color_name;
        })
        ->addColumn('size',function(Rate $rate){
            return $rate->size_height_width;
        })
        ->addColumn('options',function(Rate $rate) use ($auth_user){
            
             $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


                if($auth_user->can('rate-view')){
                    $action .='<a class="dropdown-item view-rate" href="'.route('rates.show',$rate->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
                }

                if($auth_user->can('rate-edit')){
                    $action .='<a class="dropdown-item edit-rate" href="'.route('rates.edit',$rate->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
                }
                
                if($auth_user->can('rate-delete')){
                $action .=
                
                                    \Form::open(["route" => ["rates.destroy", $rate->id], "method" => "DELETE"] ).'
                                          <button type="submit" class="dropdown-item btn btn-link delete-button" ><i class="fa fa-trash"></i> '.trans("Delete").'</button> 
                                        '.\Form::close();
                }

                $action .=     '</div>
                                            </div>';
                return $action;

        })
        ->filterColumn('size', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
               $query->from('sizes')->whereRaw("sizes.id = products.size_id AND CONCAT(sizes.height,' x ',sizes.width) like ?", ["%{$keyword}%"]);
           });
        })
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })
        ->rawColumns(['options'])
            
        ->toJson();           
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('rate-create') || !request()->ajax()){
            abort(403);
        }

        $fabric_types = FabricType::with('colors')->get();
        $sizes = Size::get();
        
        
        return view('rates.create',compact('fabric_types','sizes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'fabric_types'=>'required|exists:fabric_types,id',
            'rate'=>'required',
        ]);

    $fabric_color_id = null;
    $size_id = null;
            
    if($request->has('fabric_colors')){
        if($request->fabric_colors == 'Any Color'){
            $fabric_color_id = null;
        }else{
            FabricColor::findOrFail($request->fabric_colors); 
            $fabric_color_id =$request->fabric_colors;
        }
    }

    if($request->has('size')){
        if($request->size =='Any Size'){
            $size_id = null;
        }else{
            Size::findOrFail($request->size);
            $size_id =$request->size; 
        }
    }

        $data = $request->except('fabric_types','fabric_colors','size');
        
        $data['fabric_type_id'] = $fabric_type_id = $request->fabric_types;
        $data['fabric_color_id'] =  $request->fabric_colors;

        $data['size_id'] = $request->size;

        $data['rate'] = round($data['rate'],2);

        $model = Rate::when($fabric_type_id,function ($query, $fabric_type_id) {
                    return $query->where('fabric_type_id', $fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->exists();

        if($model){
            return response()->json(
                ['success'=>false,
                'message'=>'Already have this combination rate.',
                ]
            );
        }
        
        Rate::firstOrCreate($data);
        
        return response()->json([
            'success'=>true,
            'title'=> 'Rate added successfully.',
            'subtitle'=>'',
            'redirect'=>false
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Rate $rate)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('rate-view') || !request()->ajax()){
            abort(403);
        }
        return view('rates.show',compact('rate'));
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Rate $rate)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('rate-edit') || !request()->ajax()){
            abort(403);
        }
        $fabric_types = FabricType::with('colors')->get();
        $sizes = Size::get();
        
        return view('rates.edit',compact('rate','fabric_types','sizes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Rate $rate)
    {
         $request->validate([
            'fabric_types'=>'required|exists:fabric_types,id',
            'rate'=>'required',
        ]);

    $fabric_color_id = null;
    $size_id = null;
            
    if($request->has('fabric_colors')){
        if($request->fabric_colors == 'Any Color'){
            $fabric_color_id = null;
        }else{
            FabricColor::findOrFail($request->fabric_colors); 
            $fabric_color_id =$request->fabric_colors;
        }
    }

    if($request->has('size')){
        if($request->size =='Any Size'){
            $size_id = null;
        }else{
            Size::findOrFail($request->size); 
            $size_id = $request->size;
        }
    }

        $data = $request->except('fabric_types','fabric_colors','size');
        
        $data['fabric_type_id'] = $fabric_type_id = $request->fabric_types;
        $data['fabric_color_id'] =  $request->fabric_colors;

        $data['size_id'] = $request->size;

        $data['rate'] = round($data['rate'],2);
        
        $model = Rate::where('id','!=',$rate->id)->when($fabric_type_id,function ($query, $fabric_type_id) {
                    return $query->where('fabric_type_id', $fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->exists();

        if($model){
            return response()->json(
                ['success'=>false,
                'message'=>'Already have this combination rate.',
                ]
            );
        }

        $rate->update($data);
        
        return response()->json([
            'success'=>true,
            'title'=> 'Rate updated successfully.',
            'subtitle'=>'',
            'redirect'=>false
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rate $rate)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('rate-delete')){
            abort(403);
        }
        $rate->delete();
        return redirect('rates')->with('message', 'Rate deleted successfully');
    }
}
