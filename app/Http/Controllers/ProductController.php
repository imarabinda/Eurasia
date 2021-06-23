<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

use App\Models\Production;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\FabricType;
use App\Models\FabricColor;
use App\Models\Size;
use App\Models\WeltedEdgesColor;
use App\Models\ThreadColor;
use App\Http\Controllers\FileController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Datatables;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-manage')){
            abort(403);
        }
        
        $title = 'Manage Products';
        return view('products.index',compact('title'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select_index(Production $production)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('production-add-product')){
            abort(403);
        }
        
        $id = $production->id;
        $title = 'Select Products for || '.$production->vendor_name;
        return view('products.index',compact('title','id'));
    }


    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-create')){
            abort(403);
        }

        $product_categories = ProductCategory::with('types')->get();
        
        $sizes = Size::get();
        
        $welted_edges_colors = WeltedEdgesColor::get();
        
        $thread_colors = ThreadColor::get();
        
        $title = 'Add New Product';
        return view('products.create',compact('product_categories','sizes','welted_edges_colors','thread_colors','title'));
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
        if(!$auth_user->can('product-create')){
            abort(403);
        }
        
        $request->validate([
            'product_name'=>'required|max:255',
            'product_id'=>'required|unique:products,code|max:255',
            
            'rate'=>'required',
            
            'number_of_stitches'=>'required|integer',
            
            'thread_color_description'=>'required|array|min:1',
            
            'product_categories'=>'required|exists:product_categories,id',
            
            'product_types'=>'required_if:product_categories,exists:product_category_types,product_category_id|exists:product_types,id',
            'fabric_types'=>'required_if:product_types,exists:product_fabric_types,product_type_id|exists:fabric_types,id',
            'fabric_colors'=>'required_if:fabric_types,exists:fabric_type_colors,fabric_type_id|exists:fabric_colors,id',
            
            'size'=>'required|exists:sizes,id',
            'is_welted_edges_color'=>'present',
            'welted_edges_color'=>'required_if:is_welted_edges_color,"yes"|present|nullable|exists:welted_edges_colors,id',
            
            'image_files'=>'required',
            'dst_files'=>'required',
            'emb_files'=>'required',
            
            ]
        );
        
        
        $data = $request->except('image', 'file', 'prev_img','barcode','cad_emb','cad_dst');
        $data['name'] = htmlspecialchars(trim($data['product_name']));
        $data['code'] = $data['product_id'];
        
        $data['description'] = str_replace('"', '@', $data['product_description']);
        
        $data['product_category_id'] = $data['product_categories'];
        
        
        $rate = ($data['number_of_stitches']/1000)*2;
        $data['rate'] = round($rate,2);

        
        if($request->has('product_types')){
            $data['product_type_id'] = $data['product_types'];
        }
        
        if($request->has('fabric_types')){
            $data['fabric_type_id'] = $data['fabric_types'];
        }
        
        if($request->has('fabric_colors')){
            $data['fabric_color_id'] = $data['fabric_colors'];
        }
        
        $data['size_id'] = $data['size'];
        
        
        if($data['is_welted_edges_color'] =='true'){
            $data['welted_edges_color_id'] = $data['welted_edges_color'];
        }
        
       
        
        
        $files = array(
            'image_files'=>array(
                'condition'=>'png,jpg,jpeg'
            ),
            'emb_files'=>array(
                'condition'=>'emb',
                'original_name'=>true,
            ),
            'dst_files'=>array(
                'condition'=>'dst',
                'original_name'=>true,
            ),
            'barcode_files'=>array(
                'condition'=>'btw',
                'original_name'=>true,
            ),
            'psd_files'=>array(
                'condition'=>'psd',
                'original_name'=>true,
            ),
        );

       
        $sums = array();
    
        foreach($files as $file_param => $values){
            
            if(is_numeric($file_param) && !is_array($values)){
                $file_param = $values;
            }

            if($request->has($file_param)){
                    $sums[$file_param] = $request->file($file_param);
            }
            
            //condition
            if(is_array($values) && array_key_exists('condition',$values)){
                    $sums[$file_param][$file_param.'_condition'] = $values['condition'];
            }

            //original_name
            if(is_array($values) && array_key_exists('original_name',$values)){
                    $sums[$file_param]['original_name'] = $values['original_name'];
            }


            //old
            $old = $file_param.'_old';
            if($request->has($old)){
                $sums[$file_param][$old] = $request->$old;
            }

        }

        $product = Product::create($data);
        $file = new FileController();
        
        $file->files($sums,$product,'files');

        $product->thread_colors()->sync($this->mapThreadColors($data['thread_color_description']));
        
        return response()->json(array('success'=>true,   
         'redirect'=>route('products.index')
        ));    
    
    }
    
    /***
     * Add description to product thread color description
     */
    
    private function mapThreadColors($thread_colors){
        return collect($thread_colors)->map(function($i){
            return ['description'=>$i];
        });
    }
    
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-view')){
            abort(403);
        }
        
        $product_categories = ProductCategory::with('types')->get();
        
        $fabric_types = FabricType::with('colors')->get();
        
        $sizes = Size::get();
        
        $welted_edges_colors = WeltedEdgesColor::get();
        
        $thread_colors = ThreadColor::get();
        
        $title = 'View product - '.$product->name.' | '.$product->code;
        return view('products.show',compact('title','product','product_categories','fabric_types','sizes','welted_edges_colors','thread_colors'));
        
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-edit')){
            abort(403);
        }
        
        $product->loadCount('productions');

        $product_categories = ProductCategory::with('types')->get();
        
        $fabric_types = FabricType::with('colors')->get();
        
        $sizes = Size::get();
        
        $welted_edges_colors = WeltedEdgesColor::get();
        
        $thread_colors = ThreadColor::get();
        
        $title = 'Edit product - '.$product->name.' | '.$product->code;
        return view('products.edit',compact('title','product','product_categories','fabric_types','sizes','welted_edges_colors','thread_colors'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */ 
    public function update(Request $request, Product $product)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-edit')){
            abort(403);
        }
        
        $request->validate([
            'product_name'=>'required|max:255',
            'product_id'=>'required|unique:products,code,'.$product->id,


            'rate'=>'required',
            
            'number_of_stitches'=>'required|integer',
            
            'thread_color_description'=>'required|array|min:1',
            
            'product_categories'=>'required|exists:product_categories,id',
            
            'product_types'=>'required_if:product_categories,exists:product_category_types,product_category_id|exists:product_types,id',
            'fabric_types'=>'required_if:product_types,exists:product_fabric_types,product_type_id|exists:fabric_types,id',
            'fabric_colors'=>'required_if:fabric_types,exists:fabric_type_colors,fabric_type_id|exists:fabric_colors,id',
            
            'size'=>'required|exists:sizes,id',
            
            'is_welted_edges_color'=>'present',
            'welted_edges_color'=>'required_if:is_welted_edges_color,"yes"|present|nullable|exists:welted_edges_colors,id',
            
            'image_files'=>'required_if:image_files_old,null',
            'dst_files'=>'required_if:dst_files_old,null',
            'emb_files'=>'required_if:image_files_old,null',

        ]);

        $data = $request->except('image', 'file', 'prev_img','barcode','cad_emb','cad_dst');
        
        $data['name'] = htmlspecialchars(trim($data['product_name']));
        $data['code'] = $data['product_id'];
        
        $data['description'] = str_replace('"', '@', $data['product_description']);

        $rate = ($data['number_of_stitches']/1000)*2;
        $data['rate'] = round($rate,2);
        
        $data['number_of_stitches']=$data['number_of_stitches'];

        if($product->productions->count() <= 0){

        $data['product_category_id'] = $data['product_categories'];
    
        if($request->has('product_types')){
        $data['product_type_id'] = $data['product_types'];
            }else{
            $data['product_type_id'] = null;

        }

        if($request->has('fabric_types')){
            $data['fabric_type_id'] = $data['fabric_types'];
        }else{
            $data['fabric_type_id'] = null;

        }

        if($request->has('fabric_colors')){
            $data['fabric_color_id'] = $data['fabric_colors'];
        }else{

            $data['fabric_color_id'] = null;
        }
        
        $data['size_id'] = $data['size'];
        
    }
        

        if($data['is_welted_edges_color'] =='true'){
            $data['welted_edges_color_id'] = $data['welted_edges_color'];
        }else{
            $data['welted_edges_color_id']=null;
        }
        
        

        $files = array(
            'image_files'=>array(
                'condition'=>'png,jpg,jpeg'
            ),
            'emb_files'=>array(
                'condition'=>'emb',
                'original_name'=>true,
            ),
            'dst_files'=>array(
                'condition'=>'dst',
                'original_name'=>true,
            ),
            'barcode_files'=>array(
                'condition'=>'btw',
                'original_name'=>true,
            ),
            'psd_files'=>array(
                'condition'=>'psd',
                'original_name'=>true,
            ),
        );

        $sums = array();
    
        foreach($files as $file_param => $values){
            
            if(is_numeric($file_param) && !is_array($values)){
                $file_param = $values;
            }

            if($request->has($file_param)){
                    $sums[$file_param] = $request->file($file_param);
            }
            
            if(is_array($values) && array_key_exists('condition',$values)){
                    $sums[$file_param][$file_param.'_condition'] = $values['condition'];
            }

            
            //original_name
            if(is_array($values) && array_key_exists('original_name',$values)){
                    $sums[$file_param]['original_name'] = $values['original_name'];
            }


            $old = $file_param.'_old';
            if($request->has($old)){
                $sums[$file_param][$old] = $request->$old;
            }
        }

        $file = new FileController();
        
        $file->files($sums,$product,'files');

        $product->update($data);

        $product->thread_colors()->sync($this->mapThreadColors($data['thread_color_description']));

        return response()->json(array('success'=>true,    
        'redirect'=>route('products.index')
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('product-delete')){
            abort(403);
        }
        
            if($product->productions->count() <= 0){
                $links = $product->files()->pluck('link')->toArray();
                Storage::delete($links);
                $product->delete();
            }
            return redirect('products')->with('message', 'Product deleted successfully');
    }

    
    public function delete_by_selection(Request $request){
        $ids = $request->ids;
        foreach ($ids as $id) {
            $data = Product::findOrFail($id);
            $data->save();
        }
        return 'Product deleted successfully!';
    }

    public function list(){
        $auth_user = auth()->user();
        if(!$auth_user->can('product-manage')){
            abort(403);
        }
        
        $model = Product::with(['product_category:id,name','fabric_type:id,name','fabric_color:id,name','product_type:id,name','size:id,width,height'])->select('products.*');
        return datatables()
        ->eloquent($model)
            ->addColumn('options',function(Product $product) use ($auth_user){
                $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';

                if($auth_user->can('product-view')){
                    $action .='<a class="dropdown-item" href="'.route('products.show',$product->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
                }
                if($auth_user->can('product-edit')){
                $action .='<a class="dropdown-item" href="'.route('products.edit',$product->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
                }

                if($auth_user->can('product-delete') && $product->productions->count() <= 0){        
                $action .=
                
                                    \Form::open(["route" => ["products.destroy", $product->id], "method" => "DELETE"] ).'
                                          <button type="submit" class="dropdown-item btn btn-link delete-button" ><i class="fa fa-trash"></i> '.trans("Delete").'</button> 
                                        '.\Form::close();
                }                      

                $action .=     '</div>
                                            </div>';
                return $action;
            
            })
            ->addColumn('size',function(Product $product) {
                    return $product->size_height_width;
                })
            ->addColumn('product_type', function(Product $product){
                return  $product->product_type_name;
        })
            ->addColumn('product_category', function(Product $product){
                return  $product->product_category_name;
        })
            ->addColumn('fabric_type', function(Product $product){
                return  $product->fabric_type_name;
        })
            ->addColumn('fabric_color', function(Product $product){
                return  $product->fabric_color_name;
        })  
        ->addColumn('key', function(Product $product){
                return  $product->id;
        })
        ->editColumn('code',function(Product $product)use ($auth_user){
            $content = $product->code;
            
            if($auth_user->can('product-view')){
            $content = '<a target="_blank" href="'.route('products.show',$product->id).'">'.$product->code.'</a>';
                
            }
            return $content;
        })  
        ->editColumn('name',function(Product $product)use ($auth_user){
            $content = $product->name;
            
            if($auth_user->can('product-view')){
            $content = '<a target="_blank" href="'.route('products.show',$product->id).'">'.$product->name.'</a>';
            }
            return $content;
        })  
        ->addColumn('image', function(Product $product){

            $img = $product->images->first();
                
                if($img && Storage::exists($img->link)){
                    $image = '<img src="'.Storage::url($img->link).'" height="30" width="30">';
                }else{
                    $image = '<img src="'.Storage::url("uploads/no-product-image.jpg").'" height="30" width="30">';
                }
                

                return  $image;
            })
            
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })
        ->filterColumn('size', function($query, $keyword) {
            $query->whereExists(function ($query) use ($keyword) {
               $query->from('sizes')->whereRaw("sizes.id = products.size_id AND CONCAT(sizes.height,' x ',sizes.width) like ?", ["%{$keyword}%"]);
           });
            })
                ->rawColumns(['image','options','name','code'])->toJson();

    }


    
}
