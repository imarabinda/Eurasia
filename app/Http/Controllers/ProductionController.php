<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\Stitching;
use App\Models\CutPiece;
use App\Models\Product;
use App\Models\CutPieceUseable;
use App\Models\EmbroideryStock;
use App\Models\EmbroideryStockLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ProductionController extends Controller
{

    public function index(){
        $auth_user = auth()->user();
        if(!$auth_user->can('production-manage')){
            abort(403);
        }
        
        $title = 'Manage Embroideries';
        return view('productions.index',compact('title'));
    }

     

    public function show(Production $production){
      
        $auth_user = auth()->user();
        if(!$auth_user->can('production-view')){
            abort(403);
        }
        
        $title = 'View Embroidery - '.$production->vendor_name;
        return view('productions.show',compact('production','title'));
    
    }

    public function list(){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-manage')){
            abort(403);
        }
        $model = Production::select(['productions.*']);
        $model->leftJoin('workables',function($join){
            $join->on('workables.workable_id','=','productions.id')->where('workables.workable_type','=','App\Models\Production');
        })
        ->groupBy('productions.id')
        ->selectRaw('count(workables.product_id) as products_count');
        
        return datatables()
        ->eloquent($model)
            ->addColumn('options',function(Production $production) use($auth_user){
                $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


                if($auth_user->can('production-view')){
                    $action .='<a class="dropdown-item" href="'.route('productions.show',$production->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
                }
                if($auth_user->can('production-edit')){
                    $action .='<a class="dropdown-item" href="'.route('productions.edit',$production->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
                }
                
                $action .=     '</div>
                                            </div>';
                return $action;
            
            })
            ->editColumn('vendor_name',function(Production $production)use($auth_user){
                if($auth_user->can('productions-view')){
                return '<a target="_blank" href="'.route("productions.show",$production->id).'">'.$production->vendor_name.'</a>';
                }else{
                    $production->vendor_name;
                }
            })
            ->addColumn('received_products',function(Production $production){
                 $count = array();
                foreach($production->products as $i =>$product){
                    $issued = $product->pivot->issued_quantity;
                    $get = \App\Models\EmbroideryStockLog::where([['production_id',$production->id],['product_id',$product->id]])->sum(DB::raw('received_embroidery + received_damage'));
                    if($issued == $get){
                        $count[]=$get;
                    }
                }
                return count($count);
            })
            ->orderColumn('issue_date', function ($query, $order) {
                     $query->orderBy('issue_date', $order)->orderBy('created_at','desc');
                 })

        ->filterColumn('products_count', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {     
               $query->from('workables')->havingRaw('count(workables.product_id) like ?',["%{$keyword}%"]);
           });
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
    public function embroidery_stock()
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('embroidery-stock-manage')){
            abort(403);
        }
        $title = 'Embroidery Stock';
        return view('productions.stock',compact('title'));
    }

    public function select_embroidery_stock(Stitching $stitching)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('stitching-add-product')){
            abort(403);
        }

        $id = $stitching->id;
        $title = 'Select embroidery stock for || '.$stitching->vendor_name;
        return view('productions.stock',compact('title','id'));
    }
    
    public function stock_list(Request $request){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('embroidery-stock-manage')){
            abort(403);
        }

        $model = EmbroideryStock::with(['product:id,name,code,fabric_type_id,fabric_color_id','product.fabric_type:id,name','product.fabric_color:id,name','production:id,issue_date'])->select('embroidery_stocks.*');
        
        $model->leftJoin('workables as logs',function($join){
            $join->on('logs.embroidery_stock_id','=','embroidery_stocks.id');
        })->groupBy('embroidery_stocks.id')->selectRaw("sum(issued_quantity) as total_used")->selectRaw('embroidery_stocks.received_embroidery - IFNULL(sum(issued_quantity),0) as remaining')->havingRaw("embroidery_stocks.received_embroidery - IFNULL(sum(issued_quantity),0) > ?",[0]);
        
        return datatables()
        ->eloquent($model)
        ->addColumn('key',function(EmbroideryStock $embroidery_stock){
            return $embroidery_stock->id;
        })
        ->addColumn('product_code',function(EmbroideryStock $embroidery_stock)use($auth_user){
            $content = $embroidery_stock->product->code;
            if($auth_user->can('product-view')){    
                $content = '<a target="_blank" href="'.route('products.show',$embroidery_stock->product_id).'">'.$embroidery_stock->product->code.'</a>';
            }
        return $content;
        })
        ->addColumn('product_name',function(EmbroideryStock $embroidery_stock)use($auth_user){
            $content = $embroidery_stock->product->name;
            if($auth_user->can('product-view')){    
            $content = '<a target="_blank" href="'.route('products.show',$embroidery_stock->product_id).'">'.$embroidery_stock->product->name.'</a>';
            }
            return $content;
        })
        ->filterColumn('remaining', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
              $query->from('workables')->whereColumn('workables.embroidery_stock_id','=','embroidery_stocks.id')
              ->havingRaw("(embroidery_stocks.received_embroidery - IFNULL(SUM(workables.issued_quantity),0)) like ?", ["%{$keyword}%"]);
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
        if(!$auth_user->can('production-create')){
            abort(403);
        }

        $items = null;
        if($request->session()->has('product-items')){
            $items = $request->session()->get('product-items');
            $request->session()->forget('product-items');
        }

        $request->session()->forget('stock_count.product');        
        $request->session()->forget('stock_input.product');
        
        $title = 'Add New Embroidery';
        return view('productions.create',compact('title','items'));       
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
        if(!$auth_user->can('production-create')){
            abort(403);
        }


         $request->validate([
            'issue_date'=>'required',
            'vendor_name'=>'required|max:255',
            'consignor_name'=>'required|max:255',
            
            'embroidery_stock'=>'required|array|min:1',
            
        ]);

        $data['issue_date'] = $request->issue_date;
        $data['vendor_name'] = $request->vendor_name;
        $data['vendor_gst_no'] = $request->vendor_gst_no;
        $data['vendor_address'] = $request->vendor_address;
        $data['consignor_name']=$request->consignor_name;
        $data['consignor_address']=$request->consignor_address;
        $data['consignor_gst_no']=$request->consignor_gst_no;
        $data['challan_number']= \Carbon\Carbon::now()->timestamp;

        $response=array();
        
        $calculate_stock = array();
        $after = array();
        $flag = true;
        $embroidery_stocks =$request->embroidery_stock;
        if(empty($embroidery_stocks)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }

        foreach($embroidery_stocks as $product_id=>$pieces){
            

            
            if($pieces <= 0 ){
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = "Issue embroidery stock can't be .".$pieces;
                    $flag = false;
                    break;
            }

            $product = Product::find($product_id);

            if(!$product){
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = "Invalid product.";
                    $flag = false;
                    break;
            }

            if(!$product->fabric_type_id && !$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                break;    
            }

            if(!$product->fabric_type_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                break;    
            }
         
            if(!$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                break;    
            }

            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
            $size_id = $product->size_id;
            
            if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                unset($embroidery_stocks[$product_id]);
                continue;
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
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
                    break;
                }
                
                if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
                }
                else{
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
                }
                
                $already_used = $stock->used_pieces->sum('used_pieces');
                
                $total_stock = $stock->pieces ? $stock->pieces : 0;
                $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
                if($total_stock >= $total){
                    $after[$product->id]= ['used_pieces'=>(int)$pieces,'cut_piece_id'=>$stock->id];
                }else{
                    $flag = false;
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.', maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
                    break;
                }
            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route('productions.index'); 
                $production = Production::create($data);
                $production->products()->sync($this->mapEmbroiderStock($embroidery_stocks));
                $production->cut_piece_use()->sync($this->mapCutPieceUse($after));
                return response()->json($response);
            }
        }
        
        
        
        private function mapEmbroiderStock($stocks){
            return collect($stocks)->map(function($i){
                return ['issued_quantity'=>$i];
            });
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
    public function edit(Request $request,Production $production)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-edit')){
            abort(403);
        }

        $items = null;
        
        if($request->session()->has($production->id.'-product-items') && $auth_user->can('production-add-product')){
            $items = $request->session()->get($production->id.'-product-items');
            $request->session()->forget($production->id.'-product-items');
        }


        $title = 'Edit Embroidery - '.$production->vendor_name;
        return view('productions.edit',compact('production','title','items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Production $production)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-edit')){
            abort(403);
        }


         $request->validate([
            'issue_date'=>'required',
            'vendor_name'=>'required|max:255',
        ]);

        $data['issue_date'] = $request->issue_date;
        $data['vendor_name'] = $request->vendor_name;
        $data['vendor_gst_no'] = $request->vendor_gst_no;
        $data['vendor_address'] = $request->vendor_address;
        
        // $data['consignor_name']=$request->consignor_name;
        // $data['consignor_address']=$request->consignor_address;
        // $data['consignor_gst_no']=$request->consignor_gst_no;
        // $data['challan_number']= \Carbon\Carbon::now()->timestamp;

        
        
        $response=array();
        $calculate_stock = array();
        $after = array();
        $flag = true;


        $products = $production->products()->select(['workables.issued_quantity','products.*','workables.workable_id as production_id'])->leftJoin('embroidery_stock_logs as logs',function($join){
            $join->on('logs.product_id','=','workables.product_id')->whereColumn('logs.production_id','=','workables.workable_id');
      })->groupBy('workables.product_id','logs.production_id')->selectRaw('IFNULL(sum(logs.received_damage + logs.received_embroidery ),0) as total_received');
        
        
      $products_get = $products->get();
      
    $product_ids = $products_get->pluck('pivot.issued_quantity','id');

    $embroidery_stocks = empty($request->embroidery_stock) ? [] : $request->embroidery_stock;
    
    if(!$auth_user->can('production-add-product')){
        $embroidery_stocks = array_intersect_key($product_ids,$embroidery_stocks);
    }
    
    foreach($products_get as $key => $product){
        
        if($product->total_received > 0){
            $embroidery_stocks[$product->id] = $product->issued_quantity;    
        }

        if(!$auth_user->can('production-edit-product')){
            $embroidery_stocks[$product->id] = $product->issued_quantity;    
        }

    }
    
    $delete_ids = array_diff_key($product_ids->toArray(),$embroidery_stocks);
    
        if(!empty($delete_ids) && count($delete_ids) > 0 && $auth_user->can('production-remove-product')){
            $removed_products = $products_get->whereIn('id',array_keys($delete_ids));
            
            foreach($removed_products as $key => $product){
                
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

        if(empty($product_ids)){
            $response['message'] = "Select at least one product.";
            $flag = false;
            $response['success'] = $flag;
            return response()->json($response);
        }

        foreach($embroidery_stocks as $product_id=>$pieces){
            if($pieces <= 0 ){
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = "Issue embroidery stock can't be .".$pieces;
                    $flag = false;
                    break;
            }
            $load = false;
            $product = $products_get->find($product_id);
            
            if(!$product){
                $product = Product::find($product_id);
                $load = true;
            }

            if(!$product){
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = "Invalid product.";
                    $flag = false;
                    break;
            }

            $issued_quantity = 0;
            if(!$load){
                $issued_quantity = $product->pivot->issued_quantity;
            }
            
            if(!$product->fabric_type_id && !$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type & fabric color , please remove the product.';
                break;    
            }

            if(!$product->fabric_type_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric type , please remove the product.';
                break;    
            }
         
            if(!$product->fabric_color_id){
                $flag = false;
                $response['element']="input[name='embroidery_stock[$product_id]']";
                $response['message'] = 'Product: '.$product->name.' , Item ID: '.$product->code .', has no fabric color , please remove the product.';
                break;    
            }

            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
            $size_id = $product->size_id;
            
            if(is_null($fabric_color_id) || is_null($fabric_type_id) || is_null($size_id)){
                unset($embroidery_stocks[$product_id]);
                continue;
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
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' ,  no cutpieces available.';
                    break;
                }
                
                if(!$load && $product->total_received > 0){
                $embroidery_stocks[$product_id] = $issued_quantity;
                $after[$product->id] = [ 'cut_piece_id' => $stock->id , 'used_pieces' => (int)$issued_quantity ];
                continue;
                }
                
                if(!array_key_exists($fabric_type_id.'_'.$fabric_color_id.'_'.$size_id,$calculate_stock)){
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] = $pieces;
                }
                else{
                    $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] += $pieces;
                }
                
                $already_used = abs($stock->used_pieces->sum('used_pieces') - $issued_quantity);
                
                $total_stock = $stock->pieces ? $stock->pieces : 0;
                $total = $calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id] + $already_used;
                
                if($total_stock >= $total){
                    $after[$product->id] = [ 'cut_piece_id' => $stock->id , 'used_pieces' => (int)$pieces ];
                }else{
                    $flag = false;
                    $response['element']="input[name='embroidery_stock[$product_id]']";
                    $response['message'] = 'Product: '.$product->name.' - '.$product->code.', '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.', maximum allowed cut piece quantity '.$total_stock.' , already used '.$already_used.' , addition of same fabric type , fabric color and size is '.$calculate_stock[$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id];
                    break;
                }
            }
            
            $response['success'] = $flag;
            if(!$flag){
                return response()->json($response);
            }else{
                $response['redirect'] = route( 'productions.index'); 
                $response['title'] = 'Production updated successfully.';
                $response['subtitle'] = 'Redirecting to manage productions.';
                
                if($request->add_clicked != 'false'){
                    $response['title'] = 'Redirecting to add products.';
                    $response['subtitle'] = '';
                    
                    $response['redirect'] = route( 'products.select',$production->id); 
                }
                $production->cut_piece_use()->sync($this->mapCutPieceUse($after));
                $products->sync($this->mapEmbroiderStock($embroidery_stocks));                
                $production->update($data);
                return response()->json($response);
            }
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Production $production)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-delete')){
            abort(403);
        }

        //
    }


    public function save_challan(Request $request,Production $production){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-save-challan')){
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
        
        $production->update($data);

        return response()->json(array('success'=>true));
    }


    public function receive(Request $request,$id){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-receive')){
            abort(403);
        }

         $request->validate([
            'received_embroidery'=>'integer',
            'received_damage'=>'integer',
        ]);

        
        $data['received_embroidery']= $request->received_embroidery;
        $data['received_damage']= $request->received_damage;
        
        $data['product_id'] =Product::where('code',$request->product)->value('id');
        if(empty($data['product_id'])){
            abort(404,'Invalid product.');
        }
        $data['production_id']=$id;
        
        $return = array(
            'success'=>false,
        );
        
        $production = Production::find($id)->products()->find($data['product_id']);
        if(!$production){
            abort(404,'Invalid production.');
        }
        $total_issued = $production->pivot->issued_quantity;
        
        $embroidery_stock_log = EmbroideryStockLog::where([['production_id',$id],['product_id',$data['product_id']]])->groupBy('production_id','product_id')->selectRaw('sum(received_embroidery) as total_received_embroidery')->selectRaw('sum(received_damage) as total_received_damage')->get()->first();
        
        
        $total_received_embroidery = 0;
        $total_received_damage = 0;
        
        if($embroidery_stock_log){
            $total_received_embroidery = $embroidery_stock_log->total_received_embroidery;
            $total_received_damage = $embroidery_stock_log->total_received_damage;
        }
        
        $entered = $data['received_embroidery']+ $data['received_damage'];
        $received_already = $total_received_embroidery + $total_received_damage ;
        $now_total = $received_already + $entered;
        
        if($now_total > $total_issued){
            $return['message']= "Can't create receive cause it exceeds embroidery stock. Entered: ".$entered." , Remaining : ".($total_issued-$received_already)." , Total : ".$now_total." (already received + entered)";
        }else{
            EmbroideryStockLog::create(['production_id'=>$data['production_id'],'product_id'=>$data['product_id'],'received_embroidery'=>$data['received_embroidery'],'received_damage'=>$data['received_damage']]);
            $import = EmbroideryStock::firstOrCreate(
                    ['product_id'=>$data['product_id']],
                    ['production_id'=>$data['production_id'],'product_id'=>$data['product_id'],'received_embroidery'=>$data['received_embroidery'],'received_damage'=>$data['received_damage']]
                );
                if(!$import->wasRecentlyCreated){
                    $import->increment('received_damage',$data['received_damage']);
                    $import->increment('received_embroidery',$data['received_embroidery']);
                }

            $return['success'] = true;
            $return['receive'] = $data['received_embroidery'] + $total_received_embroidery;
            $return['damage'] = $data['received_damage']+ $total_received_damage;

           }
        return response()->json($return);
    }


    public function print(Production $production){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-print')){
            abort(403);
        }

            return view('productions.print',['except_nav'=>true,'production'=>$production,'title'=>'Print Production '.$production->vendor_name]);
    }


    
    public function products(){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-view')){
            abort(403);
        }

        $model = Production::find(request()->input('id'))->products()->with(['fabric_type:id,name','fabric_color:id,name','product_category:id,name','size:id,height,width']);
        $model->select(['workables.issued_quantity','products.*','workables.workable_id as production_id'])->leftJoin('embroidery_stock_logs as logs',function($join){
            $join->on('logs.product_id','=','workables.product_id')->whereColumn('logs.production_id','=','workables.workable_id');
        })->groupBy('workables.product_id','logs.production_id')->selectRaw('IFNULL(sum(logs.received_damage),0) as log_received_damage')->selectRaw('IFNULL(sum(logs.received_embroidery),0) as log_received_embroideries');
       
        return datatables()
        ->eloquent($model)
            ->addColumn('product_code',function(Product $product)use($auth_user){

                $content = $product->code;
            if($auth_user->can('product-view')){    
            
            $content = '<a target="_blank" href="'.route('products.show',$product->id).'">'.$product->code.'</a>';
            }
            return $content;
        })
        ->addColumn('product_name',function(Product $product)use ($auth_user){
            $content = $product->name;
            if($auth_user->can('product-view')){    
            
            $content = '<a target="_blank" href="'.route('products.show',$product->id).'">'.$product->name.'</a>';
            }
            return $content;
        })
        ->editColumn('issued_quantity',function(Product $product){
                return $product->issued_quantity;
            
            // return "<input value='$product->issued_quantity' name='embroidery_stock[$product->id]' type='number' min='0' class='form-control' >";
        })
        ->addColumn('size',function(Product $product){
            $content = $product->size_height_width;
            return $content;
        })
        ->addColumn('trash',function(Product $product)use($auth_user){
            if($product->log_received_damage > 0 || $product->log_received_embroideries > 0 || !$auth_user->can('production-remove-product')){
                return '';
            }
                return '<button type="button" class="rbtnDel btn btn-sm btn-danger">X</button>';
            
        })
        ->addColumn('product_id',function(Product $product){
            return $product->id;
        })
        ->addColumn('options',function(Product $product) use ($auth_user) {
            
            
            if(!$auth_user->can('production-receive')){
                return '';
            }
            $t = $product->log_received_embroideries + $product->log_received_damage;
            if($t < $product->issued_quantity){
                                $content =  '<input type="button" value="Receive" data-product="'.$product->name.'" data-code="'.$product->code.'" data-id="'.request()->input('id').'" class="btn btn-danger receive-button">';
            }else{
                                 $content = '<input type="button" value="Totally Received"  class="btn btn-success">';
                                 }
                                 return $content;
        })
        
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('products.id', $order);
        })
        ->rawColumns(['product_code','product_name','options','trash','issued_quantity'])
           
        ->toJson();
    }

    public function history(){
        
        $auth_user = auth()->user();
        if(!$auth_user->can('production-manage-history')){
            abort(403);
        }

        $model = EmbroideryStockLog::where('product_id',request()->input('product_id'))->where('production_id',request()->input('production_id'));
        return datatables()
        ->eloquent($model)
        ->editColumn('created_at',function(EmbroideryStockLog $embroidery_stock_log){
            return date('l jS \of F Y h:i:s A',strtotime($embroidery_stock_log->created_at));
        })
        ->toJson();
    }


    
    public function bucket(Request $request){
        
        $auth_user = auth()->user();
        

        if($request->has('id')){
            if(!$auth_user->can('production-edit')){
            abort(403);
            }

        $request->session()->put($request->id.'-product-items',$request->items);
            return response()->json(array(
                'success'=>true,
                'redirect'=>route('productions.edit',$request->id)
            ));    
        }

        if(!$auth_user->can('production-create')){
            abort(403);
        }

        $request->session()->put('product-items',$request->items);
        return response()->json(array(
            'success'=>true,
            'redirect'=>route('productions.create')
        ));
    }
}
