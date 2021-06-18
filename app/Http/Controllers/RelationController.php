<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\EmbroideryStock;
use App\Models\FinalStock;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;
use App\Models\WeltedEdgesColor;
use App\Models\ThreadColor;
use App\Models\CutPiece;
use Illuminate\Support\Facades\DB;

class RelationController extends Controller
{
    /***
     * Switch request for conditions
     */
    public function switch(Request $request){
        switch($request->for){
            case 'product_types':
                $product_types = ProductCategory::find($request->id)->types()->get();
                    
                $t =  '<div class="form-group">
                                        <label>Product Type *</strong> </label>
                                          <select  error-text="Select product type" name="'.$request->for.'" relation="fabric_types" required class="form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                                          <option></option>';
                                           foreach($product_types as $product_type){
                                                $text= $product_type->fabric_types()->count() > 0 ? 1 : '';
                                                $t .= '<option value="'.$product_type->id.'" is-relation="'.$text.'" >'.$product_type->name.'</option>';
                                            }
                $t.= '</select><span class="validation-msg" ></span></div>';
                echo $t;
                break;
            case 'fabric_types':
                $values = ProductType::find($request->id)->fabric_types()->get();
                $t =  '<div class="form-group">
                                        <label>Fabric Type *</strong> </label>
                                          <select error-text="Select fabric type" name="'.$request->for.'" relation="fabric_colors" required class="form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                                          <option></option>';
                                            foreach($values as $value){
                                                $text= $value->colors()->count() > 0 ? 1 : '';
                                                $t .= '<option value="'.$value->id.'" is-relation="'.$text.'" >'.$value->name.'</option>';
                                            }
                $t.= '</select><span class="validation-msg" ></span></div>';
                echo $t;
                break; 
            case 'fabric_colors':
                
                $ignore = $request->has('fabric_color');

                $values = FabricType::find($request->id)->colors()->get();
                $asterik = $ignore ? '': '*';
                $first_option = $ignore ? 'Any Color': '';
                
                $t =  '<div class="form-group">
                                        <label>Fabric Color'.$asterik.' </strong> </label>
                                          <select error-text="Select fabric color" name="'.$request->for.'" required class="form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                                          <option>'.$first_option.'</option>';
                                            foreach($values as $value){
                                                $t .= '<option value="'.$value->id.'" >'.$value->name.'</option>';
                                            }
                $t.= '</select><span class="validation-msg" ></span></div>';
                echo $t;
                break;
        }
    }




    public function get_product_cut_pieces_stock(Request $request){
        
        if(!$request->has('unique') || !$request->has('on') || !$request->ajax()){
                return false;
            }
            
                $product = Product::where('code',$request->unique)->with('fabric_type','fabric_color','size')->first();
                
                if(!$product){
                    abort(404,'Invalid product.');
                }

                $fabric_type_id = $product->fabric_type_id;
                $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;
                $reference=$request->on;
                $input=$request->input;
                
                    $return  = array(
                        'value'=>$input,
                        'success'=>false
                    );

                    
        if($input <= 0){
            $response['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue stitching stock can't be .".$input;  
            return response()->json($return);
        }
        
                
                $prev_value = $request->session()->get('stock_input.product.'.'prev_'.$reference, 0);
                $total = $request->session()->get('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, 0);
                
                if($request->has('remove') && $request->remove == 'true'){
                    $request->session()->put('stock_input.product.'.'prev_'.$reference, abs($prev_value - $input));
                    $request->session()->put('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total-$input));
                    return true;
                }
                
                $increased = ($input-$prev_value);
                $total_addition = $total + $increased;   
                $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                    return $query->where('fabric_type_id',$fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->withSum('used_pieces','used_pieces')->first();

                if(!$stock){
                    
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' no cut pieces available.';
                
                    return response()->json($return);
                }
                $stock_pieces = $stock->pieces ?$stock->pieces:0;
                $total_quantity = $stock_pieces - $stock->used_pieces()->sum('used_pieces');
                
               
                
                
                if($total_addition > $total_quantity){
                    $return['value']= $input-abs($total_quantity-$total_addition);
                    $request->session()->put('stock_input.product.'.'prev_'.$reference, $input-abs($total_quantity-$total_addition));
                    $request->session()->put('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, $total_quantity);
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum available '.$total_quantity.' , adjusted to '.($input-abs($total_quantity-$total_addition));
                }
                else if($total_addition <= $total_quantity){
                    $request->session()->put('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total+$increased));
                    $request->session()->put('stock_input.product.'.'prev_'.$reference, $input);
                    $return['value']= $input;
                }
                else if($total_addition == $total_quantity){
                $request->session()->put('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                $request->session()->put('stock_input.product.'.'prev_'.$reference, $input);
                $return['value']= $input;
            }
            else if($total_addition != $total_quantity){
                    $request->session()->put('stock_input.product.'.'prev_'.$reference, $input);
                    $request->session()->put('stock_count.product.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                    $return['value']= $input;
                }
                else if($input > $total_quantity){
                    $request->session()->put('stock_input.product.'.'prev_'.$reference, $input-$total_quantity-$total_addition);
                    $return['value']= $input-abs($total_quantity-$total_addition);
                }
                return response()->json($return);
            }
            
            



public function get_embroidery_cut_pieces_stock(Request $request){
    
    if(!$request->has('unique') || !$request->has('on') || !$request->ajax()){
        return false;
    }
    
    $input=$request->input;
        
    $return  = array(
        'value'=>$input,
        'success'=>false
    );
    
    $embroidery_stock_id = $request->unique;;
    $return['element']="input[name='stitching_stock[$embroidery_stock_id]']";
    $embroidery_stock = EmbroideryStock::find($request->unique);
   
    
        if(!$embroidery_stock){
            abort(404,'Invalid embroidery stock.');
        }

        
        $embroidery_stock->load('product');
        
        $product = $embroidery_stock->product;
        
        
        if(!$product){
            $return['message'] = "Invalid embroidery product.";
            return response()->json($return);
        }
        

        


            
            $product->load('fabric_type','fabric_color','size');
            
            if(!$product->fabric_type_id){
                $return['message'] = 'Product: '.$product->name.' , Code: '.$product->code .', has no fabric type , please remove the product.';
            return response()->json($return);
            }
            if(!$product->fabric_color_id){
                $return['message'] = 'Product: '.$product->name.' , Code: '.$product->code .', has no fabric color , please remove the product.';
            return response()->json($return);
            }

            
        if($input <= 0){
            $response['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue stitching stock can't be .".$input;  
            return response()->json($return);
        }
        

        $issued_quantity = $embroidery_stock->stitches()->sum('issued_quantity');
        $now_t = $issued_quantity + $input;
        
        $return['embroidery_stock'] = $embroidery_stock->received_embroidery;
            
        if($now_t > $embroidery_stock->received_embroidery){
            
            $return['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$input." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery.", already issued ".$issued_quantity;
            
            return response()->json($return);
        }
        if($embroidery_stock->received_embroidery < $input){
            $return['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$input." can't be greater than embroidery stock quantity ".$embroidery_stock->received_embroidery;
            return response()->json($return);
        }
        
        
        
            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;
                $reference=$request->on;

                
                
                $prev_value = $request->session()->get('stock_input.embroidery.'.'prev_'.$reference, 0);
                $total = $request->session()->get('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, 0);
                
                
                if($request->has('remove') && $request->remove == 'true'){
                    $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, abs($prev_value - $input));
                    $request->session()->put('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total-$input));
                    
                    return true;
                }
                
                $increased = ($input-$prev_value);
                $total_addition = $total + $increased;   
                $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                    return $query->where('fabric_type_id',$fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->withSum('used_pieces','used_pieces')->first();

                
                if(!$stock){
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' no cut pieces available.';
                    return response()->json($return);
                }

                $stock_pieces = $stock->pieces ?$stock->pieces:0;
                $total_quantity = $stock_pieces - $stock->used_pieces()->sum('used_pieces');
                
               
                
                
                if($total_addition > $total_quantity){
                    $return['value']= $input-abs($total_quantity-$total_addition);
                    $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, $input-abs($total_quantity-$total_addition));
                    $request->session()->put('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, $total_quantity);
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum available '.$total_quantity.' , adjusted to '.($input-abs($total_quantity-$total_addition));
                }
                else if($total_addition <= $total_quantity){
                    $request->session()->put('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total+$increased));
                    $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, $input);
                    $return['value']= $input;
                }
                else if($total_addition == $total_quantity){
                $request->session()->put('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, $input);
                $return['value']= $input;
            }
            else if($total_addition != $total_quantity){
                    $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, $input);
                    $request->session()->put('stock_count.embroidery.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                    $return['value']= $input;
                }
                else if($input > $total_quantity){
                    $request->session()->put('stock_input.embroidery.'.'prev_'.$reference, $input-$total_quantity-$total_addition);
                    $return['value']= $input-abs($total_quantity-$total_addition);
                }
                
                return response()->json($return);
    }



    
public function get_final_cut_pieces_stock(Request $request){
    
    if(!$request->has('unique') || !$request->has('on') || !$request->ajax()){
        return false;
    }
    
    $input=$request->input;
        
    $return  = array(
        'value'=>$input,
        'success'=>false
    );
    
    $final_stock_id = $request->unique;;
    $return['element']="input[name='stitching_stock[$final_stock_id]']";
    $final_stock = FinalStock::find($request->unique);
   
    
        if(!$final_stock){
            abort(404,'Invalid final stock.');
        }

        
        $final_stock->load('product');
        
        $product = $final_stock->product;
        
        
        if(!$product){
            $return['message'] = "Invalid final product.";
            return response()->json($return);
        }
        

            $product->load('fabric_type','fabric_color','size');
            
            if(!$product->fabric_type_id){
                $return['message'] = 'Product: '.$product->name.' , Code: '.$product->code .', has no fabric type , please remove the product.';
            return response()->json($return);
            }
            if(!$product->fabric_color_id){
                $return['message'] = 'Product: '.$product->name.' , Code: '.$product->code .', has no fabric color , please remove the product.';
            return response()->json($return);
            }

            
        if($input <= 0){
            $response['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue stitching stock can't be .".$input;  
            return response()->json($return);
        }
        

        $issued_quantity = $final_stock->shipments()->sum('issued_quantity');
        $now_t = $issued_quantity + $input;
        
        $return['final_stock'] = $final_stock->received_stitches;
            
        if($now_t > $final_stock->received_stitches){
            
            $return['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$input." can't be greater than final stock quantity ".$final_stock->received_stitches.", already issued ".$issued_quantity;
            
            return response()->json($return);
        }
        if($final_stock->received_stitches < $input){
            $return['message'] = 'Product: '.$product->name.' - '.$product->fabric_type_name.' - '.$product->fabric_color_name.' , '.$product->size_height_width." Issue Stitching quantity ".$input." can't be greater than final stock quantity ".$final_stock->received_stitches;
            return response()->json($return);
        }
        
        
        
            $fabric_type_id = $product->fabric_type_id;
            $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;
                $reference=$request->on;

                
                
                $prev_value = $request->session()->get('stock_input.final.'.'prev_'.$reference, 0);
                $total = $request->session()->get('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, 0);
                
                
                if($request->has('remove') && $request->remove == 'true'){
                    $request->session()->put('stock_input.final.'.'prev_'.$reference, abs($prev_value - $input));
                    $request->session()->put('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total-$input));
                    
                    return true;
                }
                
                $increased = ($input-$prev_value);
                $total_addition = $total + $increased;   
                $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                    return $query->where('fabric_type_id',$fabric_type_id);
                })->when($fabric_color_id,function($query,$fabric_color_id){
                    return $query->where('fabric_color_id',$fabric_color_id);
                })->when($size_id,function($query,$size_id){
                    return $query->where('size_id',$size_id);
                })->withSum('used_pieces','used_pieces')->first();

                
                if(!$stock){
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' no cut pieces available.';
                    return response()->json($return);
                }

                $stock_pieces = $stock->pieces ?$stock->pieces:0;
                $total_quantity = $stock_pieces - $stock->used_pieces()->sum('used_pieces');
                
               
                
                
                if($total_addition > $total_quantity){
                    $return['value']= $input-abs($total_quantity-$total_addition);
                    $request->session()->put('stock_input.final.'.'prev_'.$reference, $input-abs($total_quantity-$total_addition));
                    $request->session()->put('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, $total_quantity);
                    $return['message'] = 'Product: '.$product->name.' , '.$product->fabric_type_name.'- '.$product->fabric_color_name.' - '.$product->size_height_width.' maximum available '.$total_quantity.' , adjusted to '.($input-abs($total_quantity-$total_addition));
                }
                else if($total_addition <= $total_quantity){
                    $request->session()->put('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($total+$increased));
                    $request->session()->put('stock_input.final.'.'prev_'.$reference, $input);
                    $return['value']= $input;
                }
                else if($total_addition == $total_quantity){
                $request->session()->put('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                $request->session()->put('stock_input.final.'.'prev_'.$reference, $input);
                $return['value']= $input;
            }
            else if($total_addition != $total_quantity){
                    $request->session()->put('stock_input.final.'.'prev_'.$reference, $input);
                    $request->session()->put('stock_count.final.'.$fabric_type_id.'_'.$fabric_color_id.'_'.$size_id, abs($prev_total+$increased));
                    $return['value']= $input;
                }
                else if($input > $total_quantity){
                    $request->session()->put('stock_input.final.'.'prev_'.$reference, $input-$total_quantity-$total_addition);
                    $return['value']= $input-abs($total_quantity-$total_addition);
                }
                
                return response()->json($return);
    }


    public function search_product(Request $request){
        if($request->ajax()){
            $page = 1;

            if($request->has('page')){
                $page = $request->page;
            }
            
            $resultCount = 30;

            $offset = ($page - 1) * $resultCount;

            $products = Product::where('name', 'LIKE',  "%{$request->q}%")->orWhere('code', 'LIKE',  "%{$request->q}%")->orderBy('name')->skip($offset)->take($resultCount)->with('fabric_type','fabric_color','product_type','size','product_category')->get();
            $results=[]; 
            foreach($products as $key=>$product){

                $fabric_type_id = $product->fabric_type_id;
                $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;

                // $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                //     return $query->where('fabric_type_id',$fabric_type_id);
                // })->when($fabric_color_id,function($query,$fabric_color_id){
                //     return $query->where('fabric_color_id',$fabric_color_id);
                // })->when($size_id,function($query,$size_id){
                //     return $query->where('size_id',$size_id);
                // })->withSum('used_pieces','used_pieces')->first();
                // $used = $stock->used_pieces_sum_used_pieces == null ? 0 : $stock->used_pieces_sum_used_pieces;
                // $available = $stock->pieces - $used;
                // if($available <= 0 || empty($available)){
                //     continue;
                // }

                $data['id']=$key+1;
                $data['position']=$product->id;
                $data['name']=$product->name;
                $data['code']=$product->code;
                $data['product_category']= $product->product_category_name;
                $data['product_type']= $product->product_type_name;
                $data['fabric_type']= $product->fabric_type_name;
                $data['fabric_color']= $product->fabric_color_name;
                $data['size']= $product->size_height_width;
                $results[]=$data;
            }
            $count = Product::count();

            $morePages = ($page * $resultCount) < $count;

            $results = array(
                "results" => $results,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }


    public function search_embroidery_stock(Request $request){
        if($request->ajax()){
            $page = 1;

            if($request->has('page')){
                $page = $request->page;
            }
            
            $resultCount = 30;

            $offset = ($page - 1) * $resultCount;

            $embroidery_stocks = EmbroideryStock::select('*', \DB::raw("embroidery_stocks.id as id"))->leftJoin('products','embroidery_stocks.product_id','=','products.id')->where('products.name', 'LIKE',  "%{$request->q}%")->orWhere('products.code', 'LIKE',  "%{$request->q}%")->orderBy('name')->skip($offset)->take($resultCount)->get();
            $results=[]; 
            $embroidery_stocks->load('product')->loadSum('workables','issued_quantity');

            foreach($embroidery_stocks as $key=>$embroidery_stock){

                $product = $embroidery_stock->product;

                $fabric_type_id = $product->fabric_type_id;
                $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;

                // $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                //     return $query->where('fabric_type_id',$fabric_type_id);
                // })->when($fabric_color_id,function($query,$fabric_color_id){
                //     return $query->where('fabric_color_id',$fabric_color_id);
                // })->when($size_id,function($query,$size_id){
                //     return $query->where('size_id',$size_id);
                // })->withSum('used_pieces','used_pieces')->first();
                // $used = $stock->used_pieces_sum_used_pieces == null ? 0 : $stock->used_pieces_sum_used_pieces;
                // $available = $stock->pieces - $used;
                // if($available <= 0 || empty($available)){
                //     continue;
                // }

                $data['id']=$key+1;
                $data['position']=$embroidery_stock->id;
                $data['product']=$product->id;
                $data['name']=$product->name;
                $data['code']=$product->code;
                $data['product_category']= $product->product_category_name;
                $data['product_type']= $product->product_type_name;
                $data['fabric_type']= $product->fabric_type_name;
                $data['fabric_color']= $product->fabric_color_name;
                $data['size']= $product->size_height_width;
                $data['embroidery_stock']= $embroidery_stock->received_embroidery;
                $data['embroidery_stock_max']= $embroidery_stock->received_embroidery - $embroidery_stock->workables_sum_issued_quantity;
                $results[]=$data;
            }

            $count = EmbroideryStock::join('products','products.id','=','embroidery_stocks.product_id')->count();

            $morePages = ($page * $resultCount) < $count;

            $results = array(
                "results" => $results,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }



    
    public function search_final_stock(Request $request){
        if($request->ajax()){
            $page = 1;

            if($request->has('page')){
                $page = $request->page;
            }
            
            $resultCount = 30;

            $offset = ($page - 1) * $resultCount;

            $final_stocks = FinalStock::select('*', \DB::raw("final_stocks.id as id"))->leftJoin('products','final_stocks.product_id','=','products.id')->where('products.name', 'LIKE',  "%{$request->q}%")->orWhere('products.code', 'LIKE',  "%{$request->q}%")->orderBy('name')->skip($offset)->take($resultCount)->get();
            $results=[]; 
            if($final_stocks){
            
                $final_stocks->load('product')->loadSum('workables','issued_quantity');
        
            foreach($final_stocks as $key=>$final_stock){

                $product = $final_stock->product;

                $fabric_type_id = $product->fabric_type_id;
                $fabric_color_id = $product->fabric_color_id;
                $size_id = $product->size_id;

                // $stock = CutPiece::when($fabric_type_id,function($query,$fabric_type_id){
                //     return $query->where('fabric_type_id',$fabric_type_id);
                // })->when($fabric_color_id,function($query,$fabric_color_id){
                //     return $query->where('fabric_color_id',$fabric_color_id);
                // })->when($size_id,function($query,$size_id){
                //     return $query->where('size_id',$size_id);
                // })->withSum('used_pieces','used_pieces')->first();
                // $used = $stock->used_pieces_sum_used_pieces == null ? 0 : $stock->used_pieces_sum_used_pieces;
                // $available = $stock->pieces - $used;
                // if($available <= 0 || empty($available)){
                //     continue;
                // }

                $data['id']=$key+1;
                $data['position']=$final_stock->id;
                $data['product']=$product->id;
                $data['name']=$product->name;
                $data['code']=$product->code;
                $data['product_category']= $product->product_category_name;
                $data['product_type']= $product->product_type_name;
                $data['fabric_type']= $product->fabric_type_name;
                $data['fabric_color']= $product->fabric_color_name;
                $data['size']= $product->size_height_width;
                $data['final_stock']= $final_stock->received_stitches;
                $data['final_stock_max']= $final_stock->received_stitches - $final_stock->workables_sum_issued_quantity;
                $results[]=$data;
            }
        }
            $count = FinalStock::join('products','products.id','=','final_stocks.product_id')->count();

            $morePages = ($page * $resultCount) < $count;

            $results = array(
                "results" => $results,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

}
