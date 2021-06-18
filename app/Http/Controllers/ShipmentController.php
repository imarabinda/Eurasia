<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\CutPiece;
use App\Models\Product;
use App\Models\CutPieceUse;
use App\Models\FinalStock;
use App\Models\EmbroideryStock;
use App\Models\FinalStockLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
class ShipmentController extends Controller
{



    public function index(){
        
        $title = 'Manage Shipments';
        return view('shipments.index',compact('title'));
    }


    public function show(Shipment $shipment){

        $title = 'View shipment - '.$shipment->company_name;
        return view('shipments.show',compact('shipment','title'));

    }

    public function list(){
 
        $auth_user = auth()->user();
        if(!$auth_user->can('shipment-manage')){
            abort(403);
        }
        $model = Shipment::with('products')->select('shipments.*')->leftJoin('workables',function($join){
            $join->on('shipments.id','=','workables.workable_id')->where('workables.workable_type','=','App\Models\Shipment');
        })
        ->groupBy('shipments.id')
        ->selectRaw('count(workables.product_id) as products_count');
        
        return datatables()
        ->eloquent($model)
            
            ->addColumn('options',function(Shipment $shipment)use($auth_user){
                $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';

            if($auth_user->can('stitching-view')){
                $action .='<a class="dropdown-item" href="'.route('shipments.show',$shipment->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
            }
            if($auth_user->can('stitching-edit')){
               
            $action .='<a class="dropdown-item" href="'.route('shipments.edit',$shipment->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
            }
                $action .=     '</div>
                                            </div>';
                return $action;
            
            })
            ->editColumn('company_name',function(Shipment $shipment)use($auth_user){
                
            if($auth_user->can('stitching-view')){       
                return '<a target="_blank" href="'.route("shipments.show",$shipment->id).'">'.$shipment->company_name.'</a>';
            }
            return $shipment->company_name;
        })
            ->editColumn('shipment_id',function(Shipment $shipment)use($auth_user){
                
            if($auth_user->can('stitching-view')){
               
                return '<a target="_blank" href="'.route("shipments.show",$shipment->id).'">'.$shipment->shipment_id.'</a>';
            }
            return $shipment->shipment_id;

            })
            ->filterColumn('products_count', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('workables')->havingRaw('count(workables.product_id) like ?',["%{$keyword}%"]);
           });
        })->orderColumn('shipment_date', function ($query, $order) {
                     $query->orderBy('shipment_date', $order)->orderBy('created_at','desc');
                 })

        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })->rawColumns(['options','company_name','shipment_id'])            
        ->toJson();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $items = null;

        $auth_user = auth()->user();
        if(!$auth_user->can('shipment-create')){
            abort(403);
        }

        if($request->session()->has('final-stock-items')){
            $items = $request->session()->get('final-stock-items');
            $request->session()->forget('final-stock-items');
        }
        
        $request->session()->forget('stock_count.final');        
        $request->session()->forget('stock_input.final');
        
        $title = 'Add New Shipment';
        return view('shipments.create',compact('title','items'));       
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
        if(!$auth_user->can('shipment-create')){
            abort(403);
        }
        $data['shipment_date'] = $request->shipment_date;
        $data['shipment_id'] = $request->shipment_id;
        $data['company_name'] = $request->company_name;
        $data['note'] = $request->note;
        
        $request->validate([
            'shipment_id'=>'required|unique:shipments,shipment_id|max:255',
            'shipment_date'=>'required',
            'company_name'=>'required|max:255',
            'shipment_stock'=>'required|array|min:1',
        ]);


        $response=array();
        
        $calculate_stock = array();
        $after = array();
        $flag = true;
        $shipment_stocks = $request->shipment_stock;
        if(empty($shipment_stocks)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }
        foreach($shipment_stocks as $final_stock_id=>$pieces){
            


            $final_stock = FinalStock::find($final_stock_id);
            
            if(!$final_stock){
                    $response['element']="input[name='shipment_stock[$final_stock_id]']";
                    $response['message'] = "Invalid final stock.";
                    $flag = false;
                    break;
            }

            $issued_quantity = $final_stock->shipments->sum('pivot.issued_quantity');
                
                
            $final_stock->load('product');
            $product = $final_stock->product;
            
            if(!$product){
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = "Invalid final product.";
                $flag = false;
                break;
            }
            
            
            $product->load('fabric_type','fabric_color','size');
            
            if(!$product->fabric_type_id && !$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='final_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                break;    
            }
            
            if(!$product->fabric_type_id){
                $flag = false;
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                break;    
            }
            if(!$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                break;    
            }
            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
            $size_id = $product->size_id;
            
            if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                unset($shipment_stocks[$final_stock_id]);
                continue;
            }
            
            
            if($pieces <= 0 ){
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue shipment stock can't be .".$pieces;
                $flag = false;
                break;
            }
            
            
            $now_t = $issued_quantity + $pieces;
            
            $response['final_stock'] = $final_stock->received_stitches;
            if($now_t > $final_stock->received_stitches){
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Shipment quantity ".$pieces." can't be greater than final stock quantity ".$final_stock->received_stitches.", already issued ".$issued_quantity;
                    $flag = false;
                    break;
                }
                if($final_stock->received_stitches < $pieces){
                    $response['message'] =  'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Shipment quantity ".$pieces." can't be greater than final stock quantity ".$final_stock->received_stitches;
                    $response['element']="input[name='shipment_stock[$final_stock_id]']";
                    $flag = false;
                    break;
                }
                            
            
            // $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
            //     return $query->where('fabric_type_id',$fabric_type_id);
            // })->when($fabric_color_id,function($query,$fabric_color_id){
            //     return $query->where('fabric_color_id',$fabric_color_id);
            //     })->when($size_id,function($query,$size_id){
            //         return $query->where('size_id',$size_id);
            //     })->first();
                
            //     if(is_null($stock)){
            //         $flag = false;
            //         $response['element']="input[name='shipment_stock[$final_stock_id]']";
            //         $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
            //         break;
            //     }
                
            //     if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
            //         $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
            //     }else{
            //         $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
            //     }
            //     $already_used = $stock->used_pieces->sum('used_pieces');
                
            //     $total_stock = $stock->pieces ? $stock->pieces : 0;
            //     $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
            //     if($total_stock >= $total){
            //         $after[$product->id]= ['cut_piece_id'=>$stock->id,'used_pieces'=>(int)$pieces];
            //     }else{
            //         $flag = false;
            //         $response['element']="input[name='shipment_stock[$final_stock_id]']";
            //         $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
            //         break;
            //     }

                $product_ids[$final_stock_id] = $product->id;

            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route('shipments.index'); 
                $shipment = Shipment::create($data);
                // $shipment->cut_piece_use()->sync($after);
                $shipment->final_stocks()->sync($this->mapFinalStock($request->shipment_stock,$product_ids));
                return response()->json($response);
            }
            
        }
        
        
        
        private function mapFinalStock($stocks,$product_ids){
            foreach($stocks as $final_stock_id=>$pieces){
                $data[$final_stock_id] =array('issued_quantity'=>$pieces,'product_id'=>$product_ids[$final_stock_id]);
            }
            return $data;
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Shipment $shipment)
    {

         $auth_user = auth()->user();
        
        if(!$auth_user->can('shipment-edit')){
            abort(403);
        }

        $items = null;
        
        if($request->session()->has($shipment->id.'-final-stock-items') && $auth_user->can('shipment-add-product')){
            $items = $request->session()->get($shipment->id.'-final-stock-items');
            $request->session()->forget($shipment->id.'-final-stock-items');
        }


        $title = 'Edit shipment - '.$shipment->company_name;
        return view('shipments.edit',compact('shipment','title','items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipment $shipment)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('shipment-edit')){
            abort(403);
        }

        $request->validate([
            'shipment_id'=>'required|unique:shipments,shipment_id,'.$shipment->id.'|max:255',
            'shipment_date'=>'required',
            'company_name'=>'required|max:255',
            'shipment_stock'=>'required|array|min:1',
        ]);
        
        
        $data['shipment_date'] = $request->shipment_date;
        $data['shipment_id'] = $request->shipment_id;
        $data['company_name'] = $request->company_name;
        $data['note'] = $request->note;
        
        $response=array();
        $calculate_stock = array();
        $after = array();
        $flag = true;


        $final_stocks = $shipment->final_stocks()->with(['shipments','product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width']);

        $final_stocks_get = $final_stocks->get();
        $final_stock_ids = $final_stocks_get->pluck('pivot.issued_quantity','id');
        
        
        $shipment_stocks = empty($request->shipment_stock) ? [] : $request->shipment_stock ;

           
        if(!$auth_user->can('shipment-add-product')){
            $stitching_stocks = array_intersect_key($final_stock_ids,$shipment_stocks);
        }

        foreach($final_stocks_get as $key => $product){
          if($product->total_received > 0){
            $shipment_stocks[$product->id] = $product->issued_quantity;    
        }

        
        if(!$auth_user->can('shipment-edit-product')){
            $shipment_stocks[$product->id] = $product->issued_quantity;    
        }
        }
    
        $delete_ids = array_diff_key($final_stock_ids->toArray(),$shipment_stocks);
        
        if(!empty($delete_ids) && count($delete_ids) > 0 && $auth_user->can('shipment-remove-product')){
            $removed_final_stocks = $final_stocks_get->whereIn('id',array_keys($delete_ids));
            
            foreach($removed_final_stocks as $key => $final_stock){
                $product = $final_stock->product;

                $calculate_stock= array();
                $fabric_type_id = $product->fabric_type_id;
                $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;
            
                if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = 0 - $product->pivot->issued_quantity;
                }
                else{
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] -= $product->pivot->issued_quantity;
                }

            }    
        }

        if(empty($final_stock_ids)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }
        $product_ids = [];

        foreach($shipment_stocks as $final_stock_id => $pieces){
            $load = false;
            $final_stock = $final_stocks_get->find($final_stock_id);
            
            if(!$final_stock){
                $load = true;
                $final_stock = FinalStock::find($final_stock_id);
            }
            
            if(!$final_stock){
                    $response['element']="input[name='shipment_stock[$final_stock_id]']";
                    $response['message'] = "Invalid final stock.";
                    $flag = false;
                    break;
                }
                
                $total_issued_quantity = $final_stock->shipments->sum('pivot.issued_quantity');
                
            if($load){
                $final_stock->load('product');
            }

            $product = $final_stock->product;
            
            if(!$product){
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = "Invalid stitching product.";
                $flag = false;
                break;
            }

            $issued_quantity = 0;
            if(!$load){
             $issued_quantity = $final_stock->pivot->issued_quantity;
            }
            
             $total_issued_quantity = abs($total_issued_quantity - $issued_quantity);
             
             // if(!$load && $final_stock->total_received > 0){
                 //     $shipment_stocks[$final_stock_id] = $issued_quantity;
                 //     $product_ids[$final_stock_id] = $product->id;
                 //     continue;
                 // }
                 
                 if($load){
                     $product->load('fabric_type','fabric_color','size');
                    }
                    
                    if(!$product->fabric_type_id && !$product->fabric_color_id){
                        $flag = false;
                        $response['element']="input[name='final_stock[$product_id]']";
                        $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                        break;    
                    }
                    
                    if(!$product->fabric_type_id){
                        $flag = false;
                        $response['element']="input[name='shipment_stock[$final_stock_id]']";
                        $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                        break;    
                    }
                    if(!$product->fabric_color_id){
                        $flag = false;
                        $response['element']="input[name='shipment_stock[$final_stock_id]']";
                        $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                        break;    
                    }
                    $fabric_type_id = $product->fabric_type_id;
                    $fabric_color_id = $product->fabric_color_id;
                    $size_id = $product->size_id;
                    
                    if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                        unset($shipment_stocks[$final_stock_id]);
                        continue;
                    }
                    
                    if($pieces <= 0 ){
                        $response['element']="input[name='shipment_stock[$final_stock_id]']";
                        $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue shipment stock can't be .".$pieces;
                        $flag = false;
                        break;
                    }
                    
                    $now_t = $total_issued_quantity + $pieces;
                    
            $response['final_stock'] = $final_stock->received_stitches;
            if($now_t > $final_stock->received_stitches){
                $response['element']="input[name='shipment_stock[$final_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$final_stock->received_stitches.", already issued ".$total_issued_quantity;
                    $flag = false;
                    break;
                }
                if($final_stock->received_stitches < $pieces){
                    $response['message'] =  'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$final_stock->received_stitches;
                    $response['element']="input[name='shipment_stock[$final_stock_id]']";
                    $flag = false;
                    break;
                }            
            
            // $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
            //     return $query->where('fabric_type_id',$fabric_type_id);
            // })->when($fabric_color_id,function($query,$fabric_color_id){
            //     return $query->where('fabric_color_id',$fabric_color_id);
            //     })->when($size_id,function($query,$size_id){
            //         return $query->where('size_id',$size_id);
            //     })->first();
                
            //     if(is_null($stock)){
            //         $flag = false;
            //         $response['element']="input[name='shipment_stock[$final_stock_id]']";
            //         $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
            //         break;
            //     }
                
            //     if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
            //         $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
            //     }else{
            //         $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
            //     }
            //     $already_used = $stock->used_pieces->sum('used_pieces');
                
            //     $total_stock = $stock->pieces ? $stock->pieces : 0;
            //     $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
            //     if($total_stock >= $total){
            //         $after[$product->id]= ['cut_piece_id'=>$stock->id,'used_pieces'=>(int)$pieces];
            //     }else{
            //         $flag = false;
            //         $response['element']="input[name='shipment_stock[$final_stock_id]']";
            //         $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
            //         break;
            //     }

                $product_ids[$final_stock_id] = $product->id;
            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route( 'shipments.index'); 
                $response['title'] = 'Stitching updated successfully.';
                $response['subtitle'] = 'Redirecting to manage shipments.';
                
                if($request->add_clicked != 'false'){
                    $response['title'] = 'Redirecting to add final stocks.';
                    $response['subtitle'] = '';
                    $response['redirect'] = route( 'stitches.select_final_stock',$shipment->id); 
                }

                // $shipment->cut_piece_use()->sync($this->mapCutPieceUse($after));
                $final_stocks->sync($this->mapFinalStock($shipment_stocks,$product_ids));                
                $shipment->update($data);
                return response()->json($response);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shipment $shipment)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('shipment-delete')){
            abort(403);
        }
    }


    
    public function products(){

    $auth_user = auth()->user();
        if(!$auth_user->can('shipment-view')){
            abort(403);
        }


        $model = Shipment::find(request()->input('id'))->final_stocks()->with(['product','product.fabric_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width']);
        
        $model->select(['workables.issued_quantity','final_stocks.*'])
        // ->leftJoin('final_stock_logs as logs',function($join){
        //     $join->on('logs.product_id','=','workables.product_id')->whereColumn('logs.stitching_id','=','workables.workable_id');
        // })->groupBy('workables.product_id','logs.stitching_id')->selectRaw('IFNULL(sum(logs.received_damage),0) as log_received_damage')->selectRaw('IFNULL(sum(logs.received_stitches),0) as log_received_stitches')
        ;

    //    $model->groupBy('workables.final_stock_id')->selectRaw('final_stocks.received_stitches - IFNULL(sum(workables.issued_quantity),0) as remaining');
        

        return datatables()
        ->eloquent($model)
            ->addColumn('product_code',function(FinalStock $final_stock)use($auth_user){
                $content = $final_stock->product->code;
            if($auth_user->can('product-view')){
            
            $content = '<a target="_blank" href="'.route('products.show',$final_stock->product->id).'">'.$final_stock->product->code.'</a>';
            }
            return $content;
        })
        ->addColumn('product_name',function(FinalStock $final_stock)use($auth_user){
            $content = $final_stock->product->name;
            if($auth_user->can('product-view')){
                $content = '<a target="_blank" href="'.route('products.show',$final_stock->product->id).'">'.$final_stock->product->name.'</a>';
            }
            return $content;
        })->addColumn('size',function(FinalStock $final_stock){
            $content = $final_stock->product->size_height_width;
            return $content;
        })
        ->addColumn('trash',function(FinalStock $final_stock)use($auth_user){
            if($auth_user->can('shipment-remove-product')){
            return '<button type="button" class="rbtnDel btn btn-sm btn-danger">X</button>';
            }
            return '';
        })
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('final_stocks.id', $order);
        })
        ->rawColumns(['product_code','product_name','options','trash'])
           
        ->toJson();
    }


    public function bucket(Request $request){
 $auth_user = auth()->user();
       
        if($request->has('id')){
        
            if(!$auth_user->can('shipment-edit')){
                abort(403);
            }
        $request->session()->put($request->id.'-final-stock-items',$request->items);
            return response()->json(array(
                'success'=>true,
                'redirect'=>route('shipments.edit',$request->id)
            ));    
        }
        
        if(!$auth_user->can('stitching-create')){
            abort(403);
        }

        $request->session()->put('final-stock-items',$request->items);
        return response()->json(array(
            'success'=>true,
            'redirect'=>route('shipments.create')
        ));
    }



}
