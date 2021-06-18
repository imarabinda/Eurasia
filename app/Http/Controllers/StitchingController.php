<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stitching;
use App\Models\Shipment;
use App\Models\CutPiece;
use App\Models\Product;
use App\Models\Production;
use App\Models\CutPieceUse;
use App\Models\FinalStock;
use App\Models\EmbroideryStock;
use App\Models\FinalStockLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StitchingController extends Controller
{


    public function index(){

        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-create')){
            abort(403);
        }

        $title = 'Manage Stitches';
        return view('stitches.index',compact('title'));
    }


    public function show(Stitching $stitching){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-view')){
            abort(403);
        }

        $title = 'View stitching - '.$stitching->vendor_name;
        return view('stitches.show',compact('stitching','title'));
    }

    public function list(){
 
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-manage')){
            abort(403);
        }
        $model = Stitching::with('products','tailor')->select('stitches.*')->leftJoin('workables',function($join){
            $join->on('stitches.id','=','workables.workable_id')->where('workables.workable_type','=','App\Models\Stitching');
        })
        ->groupBy('stitches.id')
        ->selectRaw('count(workables.product_id) as products_count');
        
        return datatables()
        ->eloquent($model)
            
            ->addColumn('options',function(Stitching $stitching)use($auth_user){
                $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


                if($auth_user->can('stitching-view')){
                    $action .='<a class="dropdown-item" href="'.route('stitches.show',$stitching->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
              }
                if($auth_user->can('stitching-edit')){
                    $action .='<a class="dropdown-item" href="'.route('stitches.edit',$stitching->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
              }
                
                $action .=     '</div>
                                            </div>';
                return $action;
            
            })
            ->editColumn('vendor_name',function(Stitching $stitching) use($auth_user){

                if($auth_user->can('stitching-view')){
                    return '<a target="_blank" href="'.route("stitches.show",$stitching->id).'">'.$stitching->tailor->name.'</a>';
              }
            return $stitching->tailor->name;
            
            })
            ->addColumn('received_products',function(Stitching $stitching){
                 $count = array();
                
                foreach($stitching->products as $i =>$product){
                    $issued = $product->pivot->issued_quantity;
                    $get = \App\Models\FinalStockLog::where([['stitching_id',$stitching->id],['product_id',$product->id]])->sum(DB::raw('received_stitches + received_damage'));
                    if($issued == $get){
                        $count[]=$get;
                    }
                }
                return count($count);
            })
            ->filterColumn('products_count', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('workables')->havingRaw('count(workables.product_id) like ?',["%{$keyword}%"]);
           });
        })
        
        ->orderColumn('issue_date', function ($query, $order) {
                     $query->orderBy('issue_date', $order)->orderBy('created_at','desc');
                 })

        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })->rawColumns(['options','vendor_name'])
            
        ->toJson();



    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function stitching_stock()
    {   
        $auth_user = auth()->user();
        if(!$auth_user->can('final-stock-manage')){
            abort(403);
        }
        $title = 'Final Stock';
        return view('stitches.stock',compact('title'));
    }
    
    
    public function select_final_stock(Shipment $shipment)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('shipment-add-product')){
            abort(403);
        }

        $id = $shipment->id;
        $title = 'Select final stock for || '.$shipment->shipment_id;
        return view('stitches.stock',compact('title','id'));
    }


    public function stock_list(Request $request){
        $auth_user = auth()->user();
        if(!$auth_user->can('final-stock-manage')){
            abort(403);
        }
        $model = FinalStock::with(['product:id,name,code,size_id,fabric_type_id,fabric_color_id','product.fabric_type:id,name','product.fabric_color:id,name','product.size:id,height,width','stitching:id,issue_date'])->select('final_stocks.*');
        $model->leftJoin('workables as logs',function($join){
            $join->on('logs.final_stock_id','=','final_stocks.id');
        })->groupBy('final_stocks.id')->selectRaw("IFNULL(sum(issued_quantity),0) as total_used")->selectRaw('final_stocks.received_stitches - IFNULL(sum(issued_quantity),0) as remaining')->havingRaw("final_stocks.received_stitches - IFNULL(sum(issued_quantity),0) > ?",[0]);
        return datatables()
        ->eloquent($model)
        ->addColumn('key',function(FinalStock $final_stock){
            return $final_stock->id;
        })
    
        ->addColumn('product_code',function(FinalStock $final_stock)use($auth_user){
           if($auth_user->can('product-view')){
                $content = '<a target="_blank" href="'.route('products.show',$final_stock->product_id).'">'.$final_stock->product->code.'</a>';
            }else{
                $content = $final_stock->product->code;
            }
             return $content;
        })->addColumn('product_name',function(FinalStock $final_stock)use($auth_user){
            if($auth_user->can('product-view')){
                $content = '<a target="_blank" href="'.route('products.show',$final_stock->product_id).'">'.$final_stock->product->name.'</a>';
            }else{
                $content = $final_stock->product->name;
            }
            return $content;
        })
        
            ->editColumn('size',function(FinalStock $final_stock) {
                    return $final_stock->product->size_height_width;
                })
            
            ->filterColumn('remaining', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
              $query->from('workables')->whereColumn('workables.final_stock_id','=','final_stocks.id')->havingRaw("(final_stocks.received_stitches - IFNULL(SUM(workables.issued_quantity),0)) like ?", ["%{$keyword}%"]);
            });
            })


            ->rawColumns(['product_code','product_name'])
        ->toJson();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-create')){
            abort(403);
        }
        $items = null;

        if($request->session()->has('embroidery-stock-items')){
            $items = $request->session()->get('embroidery-stock-items');
            $request->session()->forget('embroidery-stock-items');
        }
        
        $request->session()->forget('stock_count.embroidery');        
        $request->session()->forget('stock_input.embroidery');
        
        $tailors = \App\Models\Tailor::all();
        $title = 'Add New Stitching';
        return view('stitches.create',compact('title','items','tailors'));       
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
        if(!$auth_user->can('stitching-create')){
            abort(403);
        }

         $request->validate([
            'issue_date'=>'required',
            'tailor'=>'required|exists:tailors,id',
            'consignor_name'=>'required|max:255',
            
            'stitching_stock'=>'required|array|min:1',
            
        ]);

        $data['issue_date'] = $request->issue_date;
        $data['tailor_id'] = $request->tailor;
        $data['consignor_name']=$request->consignor_name;
        $data['consignor_address']=$request->consignor_address;
        $data['consignor_gst_no']=$request->consignor_gst_no;
        $data['challan_number']= \Carbon\Carbon::now()->timestamp;
        
        $response=array();
        $calculate_stock = array();
        $after = array();
        $flag = true;
        $stitching_stocks = $request->stitching_stock;
        if(empty($stitching_stocks)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }
        foreach($stitching_stocks as $embroidery_stock_id=>$pieces){
            $embroidery_stock = EmbroideryStock::find($embroidery_stock_id);
            
            if(!$embroidery_stock){
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = "Invalid embroidery stock.";
                    $flag = false;
                    break;
            }

            $issued_quantity = $embroidery_stock->stitches->sum('pivot.issued_quantity');
            
            $embroidery_stock->load('product');
            $product = $embroidery_stock->product;
            
            if(!$product){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = "Invalid embroidery product.";
                $flag = false;
                break;
            }
            
            $product->load('fabric_type','fabric_color','size');
            
            if(!$product->fabric_type_id && !$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                break;    
            }
            
            if(!$product->fabric_type_id){
                $flag = false;
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                break;    
            }
            if(!$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                break;    
            }
            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
            $size_id = $product->size_id;
            
            if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                unset($stitching_stocks[$embroidery_stock_id]);
                continue;
            }
            
            if($pieces <= 0 ){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue stitching stock can't be .".$pieces;
                $flag = false;
                break;
            }
            
            $now_t = $issued_quantity + $pieces;
            
            $response['embroidery_stock'] = $embroidery_stock->received_embroidery;
            if($now_t > $embroidery_stock->received_embroidery){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery.", already issued ".$issued_quantity;
                    $flag = false;
                    break;
                }
                if($embroidery_stock->received_embroidery < $pieces){
                    $response['message'] =  'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $flag = false;
                    break;
                }            
            
            $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                return $query->where('fabric_type_id',$fabric_type_id);
            })->when($fabric_color_id,function($query,$fabric_color_id){
                return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->first();
                
                if(is_null($stock)){
                    $flag = false;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
                    break;
                }
                
                if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
                }else{
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
                }
                $already_used = $stock->used_pieces->sum('used_pieces');
                
                $total_stock = $stock->pieces ? $stock->pieces : 0;
                $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
                if($total_stock >= $total){
                    $after[$product->id]= ['cut_piece_id'=>$stock->id,'used_pieces'=>(int)$pieces];
                }else{
                    $flag = false;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
                    break;
                }
                $product_ids[$embroidery_stock_id] = $product->id;
            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route('stitches.index'); 
                $stitching = Stitching::create($data);
                $stitching->embroidery_stocks()->sync($this->mapStitchingStock($stitching_stocks,$product_ids));
                $stitching->cut_piece_use()->sync($this->mapCutPieceUse($after));
                return response()->json($response);
            }
            
    }
        
        private function mapStitchingStock($stocks,$product_ids){
            foreach($stocks as $embroidery_stock_id=>$pieces){
                $data[$embroidery_stock_id] =array('issued_quantity'=>$pieces,'product_id'=>$product_ids[$embroidery_stock_id]);
            }
            return $data;
        }
        
        private function mapCutPieceUse($cut_pieces){
            return collect($cut_pieces)->map(function($i){
                return $i;
            });
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Stitching $stitching)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-edit')){
            abort(403);
        }

    

        $items = null;
        if($request->session()->has($stitching->id.'-embroidery-stock-items') && $auth_user->can('stitching-add-product')){
            $items = $request->session()->get($stitching->id.'-embroidery-stock-items');
            $request->session()->forget($stitching->id.'-embroidery-stock-items');
        }

        
        $tailors = \App\Models\Tailor::all();

        $title = 'Edit Stitching - '.$stitching->vendor_name;
        return view('stitches.edit',compact('stitching','title','items','tailors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stitching $stitching)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-edit')){
            abort(403);
        }

         $request->validate([
            'issue_date'=>'required',
            'tailor'=>'required|exists:tailors,id',
        ]);

        $data['issue_date'] = $request->issue_date;
        
        $data['tailor_id'] = $request->tailor;
        
        // $data['consignor_name']=$request->consignor_name;
        // $data['consignor_address']=$request->consignor_address;
        // $data['consignor_gst_no']=$request->consignor_gst_no;
        // $data['challan_number']= \Carbon\Carbon::now()->timestamp;

        $response=array();
        $calculate_stock = array();
        $after = array();
        $flag = true;

        $embroidery_stocks = $stitching->embroidery_stocks()->with(['stitches','product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width'])
        ->select(['workables.issued_quantity','embroidery_stocks.*','workables.workable_id as stitching_id'])
        ->leftJoin('final_stock_logs as logs',function($join){
            $join->on('logs.product_id','=','workables.product_id')
            ->whereColumn('logs.stitching_id','=','workables.workable_id');
        })
        ->groupBy('workables.product_id','logs.stitching_id')
        ->selectRaw('IFNULL(sum(logs.received_damage + logs.received_stitches ),0) as total_received')
        ;

        $embroidery_stocks_get = $embroidery_stocks->get();
        $embroidery_stock_ids = $embroidery_stocks_get->pluck('pivot.issued_quantity','id');
        
        
        $stitching_stocks = empty($request->stitching_stock) ? [] : $request->stitching_stock;
            
        if(!$auth_user->can('stitching-add-product')){
            $stitching_stocks = array_intersect_key($embroidery_stock_ids,$stitching_stocks);
        }
    
        foreach($embroidery_stocks_get as $key => $product){
          if($product->total_received > 0){
            $stitching_stocks[$product->id] = $product->issued_quantity;    
        }

        
        if(!$auth_user->can('stitching-edit-product')){
            $stitching_stocks[$product->id] = $product->issued_quantity;    
        }

        }

        $delete_ids = array_diff_key($embroidery_stock_ids->toArray(),$stitching_stocks);
        
        if(!empty($delete_ids) && count($delete_ids) > 0 && $auth_user->can('stitching-remove-product')){
            $removed_embroidery_stocks = $embroidery_stocks_get->whereIn('id',array_keys($delete_ids));
            
            foreach($removed_embroidery_stocks as $key => $embroidery_stock){
                
                $product = $embroidery_stock->product;

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

        if(empty($embroidery_stock_ids)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }
        $product_ids = [];

        foreach($stitching_stocks as $embroidery_stock_id => $pieces){
            $load = false;
            $embroidery_stock = $embroidery_stocks_get->find($embroidery_stock_id);
            
            if(!$embroidery_stock){
                $load = true;
                $embroidery_stock = EmbroideryStock::find($embroidery_stock_id);
            }
            
            if(!$embroidery_stock){
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = "Invalid embroidery stock.";
                    $flag = false;
                    break;
            }
            
            $total_issued_quantity = $embroidery_stock->stitches->sum('pivot.issued_quantity');
            
            if($load){
                $embroidery_stock->load('product');
            }

            $product = $embroidery_stock->product;
            
            if(!$product){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = "Invalid embroidery product.";
                $flag = false;
                break;
            }
            
            $issued_quantity = 0;
            if(!$load){
                $issued_quantity = $embroidery_stock->pivot->issued_quantity;
            }

            $total_issued_quantity = abs($total_issued_quantity - $issued_quantity);
            
           

            if($load){
                $product->load('fabric_type','fabric_color','size');
            }
            
            if(!$product->fabric_type_id && !$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                break;    
            }
            
            if(!$product->fabric_type_id){
                $flag = false;
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                break;    
            }
            if(!$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                break;    
            }
            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
            $size_id = $product->size_id;
            
            if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                unset($stitching_stocks[$embroidery_stock_id]);
                continue;
            }
            
            if($pieces <= 0 ){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue stitching stock can't be .".$pieces;
                $flag = false;
                break;
            }
            
            
            $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                return $query->where('fabric_type_id',$fabric_type_id);
            })->when($fabric_color_id,function($query,$fabric_color_id){
                return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->first();
                
                if(is_null($stock)){
                    $flag = false;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
                    break;
                }
            
             if(!$load && $embroidery_stock->total_received > 0){
                $stitching_stocks[$embroidery_stock_id] = $issued_quantity;
                $product_ids[$embroidery_stock_id] = $product->id;
                 $after[$product->id] = [ 'cut_piece_id' => $stock->id , 'used_pieces' => (int)$issued_quantity ];
                continue;
                }
                
            $now_t = $total_issued_quantity + $pieces;
            
            $response['embroidery_stock'] = $embroidery_stock->received_embroidery;
            if($now_t > $embroidery_stock->received_embroidery){
                $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                $response['message'] = 'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery.", already issued ".$total_issued_quantity;
                    $flag = false;
                    break;
                }
                if($embroidery_stock->received_embroidery < $pieces){
                    $response['message'] =  'Product: '.$product->name.' - '.$product->code.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$pieces." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $flag = false;
                    break;
                }            
            
                
                
                if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
                }else{
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
                }
                $already_used = $stock->used_pieces->sum('used_pieces');
                
                $total_stock = $stock->pieces ? $stock->pieces : 0;
                $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
                if($total_stock >= $total){
                    $after[$product->id]= ['cut_piece_id'=>$stock->id,'used_pieces'=>(int)$pieces];
                }else{
                    $flag = false;
                    $response['element']="input[name='stitching_stock[$embroidery_stock_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
                    break;
                }
                $product_ids[$embroidery_stock_id] = $product->id;
            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route( 'stitches.index'); 
                $response['title'] = 'Stitching updated successfully.';
                $response['subtitle'] = 'Redirecting to manage stitches.';
                
                if($request->add_clicked != 'false'){
                    $response['title'] = 'Redirecting to add embroidery stocks.';
                    $response['subtitle'] = '';
                    $response['redirect'] = route( 'productions.select_embroidery_stock',$stitching->id); 
                }

                $stitching->cut_piece_use()->sync($this->mapCutPieceUse($after));
                $embroidery_stocks->sync($this->mapStitchingStock($stitching_stocks,$product_ids));                
                $stitching->update($data);
                return response()->json($response);
            }
            




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stitching $stitching)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-delete')){
            abort(403);
        }
        //
    }

    public function save_challan(Request $request,Stitching $stitching){
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-save-challan')){
            abort(403);
        }
        if(!$request->ajax()){
            return false;
        }

        
         $request->validate([
            'consignor_name'=>'required|max:255', 
        ]);

        $data['consignor_name']=$request->consignor_name;
        $data['consignor_address']=$request->consignor_address;
        $data['consignor_gst_no']=$request->consignor_gst_no;
        $stitching->update($data);
        return response()->json(array('success'=>true));
    }


    public function receive(Request $request,$id){
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-receive')){
            abort(403);
        }

        
        $request->validate([
            'received_stitches'=>'integer',
            'received_damage'=>'integer',
        ]);

        $data['received_stitches']= $request->received_stitches;
        $data['received_damage']= $request->received_damage;

        $data['product_id'] =Product::where('code',$request->product)->value('id');
        if(empty($data['product_id'])){
            abort(404,'Invalid product.');
        }
        $data['stitching_id']=$id;
        $return = array(
            'success'=>false,
        );

        $stitching = Stitching::find($id)->products()->find($data['product_id']);
        if(!$stitching){
            abort(404,'Invalid Stitching.');
        }
        $total_issued = $stitching->pivot->issued_quantity;
        
        $final_stock_log = FinalStockLog::where([['stitching_id',$id],['product_id',$data['product_id']]])->groupBy('stitching_id','product_id')->selectRaw('sum(received_stitches) as total_received_stitches')->selectRaw('sum(received_damage) as total_received_damage')->get()->first();
        
        $total_received_stitches = 0;
        $total_received_damage = 0;
        
        if($final_stock_log){
            $total_received_stitches = $final_stock_log->total_received_stitches;
            $total_received_damage = $final_stock_log->total_received_damage;
        }
        
        $entered =$data['received_stitches']+$data['received_damage'];
        $received_already = $total_received_stitches + $total_received_damage;
        $now_total =  $received_already + $entered;
        
        if($now_total > $total_issued){
            $return['message']= "Can't create receive cause it exceeds stitching stock. Entered: ".$entered." , Remaining : ".($total_issued-$received_already)." , Total : ".$now_total." (already received + entered)";
        }else{
            FinalStockLog::create(['stitching_id'=>$data['stitching_id'],'product_id'=>$data['product_id'],'received_stitches'=>$data['received_stitches'],'received_damage'=>$data['received_damage']]);
            $import = FinalStock::firstOrCreate(
                ['product_id'=>$data['product_id']],
                ['stitching_id'=>$data['stitching_id'],'product_id'=>$data['product_id'],'received_stitches'=>$data['received_stitches'],'received_damage'=>$data['received_damage']]
            );
            if(!$import->wasRecentlyCreated){
                $import->increment('received_damage',$data['received_damage']);
                $import->increment('received_stitches',$data['received_stitches']);
            }

            $return['success']=true;
            $return['receive'] = $data['received_stitches'] + $total_received_stitches;
            $return['damage'] = $data['received_damage']+ $total_received_damage;
           
        }
        return response()->json($return);
    }

    public function print(Stitching $stitching){
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-print')){
            abort(403);
        }
            return view('stitches.print',['except_nav'=>true,'stitching'=>$stitching,'title'=>'Print Stitching '.$stitching->vendor_name]);
    }

    public function products(){

        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-view')){
            abort(403);
        }

        $model = Stitching::find(request()->input('id'))->embroidery_stocks()
        ->with(['product','product.fabric_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width']);
        $model->select(['workables.issued_quantity','workables.workable_id as stitching_id','embroidery_stocks.*'])
        ->leftJoin('final_stock_logs as logs',function($join){
            $join->on('logs.product_id','=','workables.product_id')
            ->whereColumn('logs.stitching_id','=','workables.workable_id');
        })
        ->groupBy('workables.product_id','logs.stitching_id')
        ->selectRaw('IFNULL(sum(logs.received_damage),0) as log_received_damage')
        ->selectRaw('IFNULL(sum(logs.received_stitches),0) as log_received_stitches')
        
        // ->selectRaw('embroidery_stocks.received_embroidery - IFNULL(sum(workables.issued_quantity),0) as remaining WHERE embroidery_stocks.id = workables.embroidery_stock_id')
        ;

        
        return datatables()
        ->eloquent($model)
            ->addColumn('product_code',function(EmbroideryStock $embroidery_stock)use($auth_user){
            $content = $embroidery_stock->product->code;
            if($auth_user->can('product-view')){    
            $content = '<a target="_blank" href="'.route('products.show',$embroidery_stock->product->id).'">'.$embroidery_stock->product->code.'</a>';
            }
            return $content;
        })
        ->addColumn('product_id',function(EmbroideryStock $embroidery_stock){
            return $embroidery_stock->product_id;
        })
        ->addColumn('product_name',function(EmbroideryStock $embroidery_stock)use($auth_user){
            $content = $embroidery_stock->product->name;
            if($auth_user->can('product-view')){    
            $content = '<a target="_blank" href="'.route('products.show',$embroidery_stock->product->id).'">'.$embroidery_stock->product->name.'</a>';
            }
            return $content;
        })
        ->addColumn('size',function(EmbroideryStock $embroidery_stock){
            $content = $embroidery_stock->product->size_height_width;
            return $content;
        })
        ->addColumn('trash',function(EmbroideryStock $embroidery_stock)use($auth_user){
            if($embroidery_stock->log_received_damage > 0 || $embroidery_stock->log_received_stitches > 0 || !$auth_user->can('stitching-remove-product')){
                return '';
            }
            return '<button type="button" class="rbtnDel btn btn-sm btn-danger">X</button>';
        })
        ->addColumn('options',function(EmbroideryStock $embroidery_stock) use ($auth_user) {
            
            if(!$auth_user->can('production-receive')){
                return '';
            }

            $t = $embroidery_stock->log_received_stitches + $embroidery_stock->log_received_damage;
            if($t < $embroidery_stock->issued_quantity){
                                $content =  '<input type="button" value="Receive" data-product="'.$embroidery_stock->product->name.'" data-code="'.$embroidery_stock->product->code.'" data-id="'.request()->input('id').'" class="btn btn-danger receive-button">';
            }else{
                                 $content = '<input type="button" value="Totally Received"  class="btn btn-success">';
                                 }
                                 return $content;
        })
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('embroidery_stocks.id', $order);
        })
        ->rawColumns(['product_code','product_name','options','trash'])
           
        ->toJson();
    }


    
    public function history(){

        $auth_user = auth()->user();
        
        if(!$auth_user->can('stitching-manage-history')){
            abort(403);
        }

        $model = FinalStockLog::where('product_id',request()->input('product_id'))->where('stitching_id',request()->input('stitching_id'));
        
        return datatables()
        ->eloquent($model)
        ->editColumn('created_at',function(FinalStockLog $final_stock_log){
            return date('l jS \of F Y h:i:s A',strtotime($final_stock_log->created_at));
        })
        ->toJson();
    }


    public function bucket(Request $request){
 
        $auth_user = auth()->user();
        

        if($request->has('id')){
            if(!$auth_user->can('stitching-edit')){
            abort(403);
        }

        $request->session()->put($request->id.'-embroidery-stock-items',$request->items);
            return response()->json(array(
                'success'=>true,
                'redirect'=>route('stitches.edit',$request->id)
            ));    
        }
        
        
        if(!$auth_user->can('stitching-create')){
            abort(403);
        }
        $request->session()->put('embroidery-stock-items',$request->items);
        return response()->json(array(
            'success'=>true,
            'redirect'=>route('stitches.create')
        ));
    }
}
