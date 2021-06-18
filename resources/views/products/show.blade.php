@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$product->name}} || {{$product->code}}</h4>
        @can('product-edit')
         <a href="{{route('products.edit', $product->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit Product</a>
         @endcan
      </div>
   </div>
</div>
<form id="product-form">
   <div class="row">
      <div class="col-lg-12">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Item ID</label>
                        <div class="input-group">
                           <input disabled  type="text" class="form-control" error-text="Enter product id..." placeholder="Product ID" name="product_id" value="{{$product->code}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Title</label>
                        <div class="input-group">
                           <input disabled type="text" class="form-control" error-text="Enter product name..." placeholder="Product name" name="product_name" value="{{$product->name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               <div class="row form-material">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Product Description</label>
                        <div class="input-group">
                           <textarea disabled type="text" class="form-control" error-text="Enter product description..." placeholder="Product description" name="product_description">{{$product->description}}</textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               <div class="row form-material">
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Category</label>
                        {{-- <select disabled error-text="Select category" relation="product_types" required name="product_categories" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option></option>
                           @foreach($product_categories as $product_category)
                           <option @if($product->product_category_id == $product_category->id) selected="selected" @endif value="{{$product_category->id}}" is-relation="@php if(count($product_category->types) > 0) echo true; @endphp">{{$product_category->name}}</option>
                           @endforeach
                        </select> --}}
                        
                  <input disabled class="form-control" value="{{$product->product_category_name}}">
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-2" id="product_types" @if(empty($product->product_type_id)) style="display:none" @endif>
                  @if(!empty($product->product_type_id))
                  <div class="form-group">
                     @php
                     $product_types = \App\Models\ProductCategory::find($product->product_category_id)->types()->get();
                     @endphp
                     <label>Product Type</label>
                     {{-- <select disabled name="product_types" relation="fabric_types" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                        
                        <option></option>
                        @foreach($product_types as $product_type)
                        @php $text= $product_type->fabric_types()->count() > 0 ? 1 : ''; @endphp
                        <option @if($product->product_type_id == $product_type->id) selected="selected" @endif value="{{$product_type->id}}" is-relation="{{$text}}" >{{$product_type->name}}</option>';
                        @endforeach
                     </select> --}}
                     
                  <input disabled class="form-control" value="{{$product->product_type_name}}">
                     <span class="validation-msg" ></span>
                  </div>
                  @endif
               </div>
               <div class="col-md-2" id="fabric_types" @if(empty($product->fabric_type_id)) style="display:none" @endif>
               @if(!empty($product->fabric_type_id))
               @php
               $fabric_types = \App\Models\ProductType::find($product->product_type_id)->fabric_types()->get();
               @endphp
               <div class="form-group">
                  <label>Fabric Type</strong> </label>
                  {{-- <select disabled name="fabric_types" relation="fabric_colors" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                     
                     <option></option>
                     @foreach($fabric_types as $fabric_type)
                     @php $text= $fabric_type->colors()->count() > 0 ? 1 : ''; @endphp
                     <option @if($product->fabric_type_id == $fabric_type->id) selected="selected" @endif value="{{$fabric_type->id}}" is-relation="{{$text}}" >{{$fabric_type->name}}</option>';
                     @endforeach
                  </select> --}}
                  <input disabled class="form-control" value="{{$product->fabric_type_name}}">
               
               </div>
               @endif
            </div>
            <div class="col-md-2" id="fabric_colors" @if(empty($product->fabric_color_id)) style="display:none" @endif>
            @if(!empty($product->fabric_color_id))
            @php
            $colors = \App\Models\FabricType::find($product->fabric_type_id)->colors()->get();
            @endphp
            <div class="form-group">
               <label>Fabric Color</strong> </label>
               {{-- <select disabled name="fabric_colors" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Color...">
                  
                  <option></option>
                  @foreach($colors as $color)
                  <option @if($product->fabric_color_id == $color->id) selected="selected" @endif value="{{$color->id}}" >{{$color->name}}</option>';
                  @endforeach
               </select> --}}
               
                  <input disabled class="form-control" value="{{$product->fabric_color_name}}">
               <span class="validation-msg" ></span>
            </div>
            @endif
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Size</label>
               {{-- <select disabled error-text="Select product size" name="size" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Size...">
                  <option></option>
                  @foreach($sizes as $id => $size)  
                  <option value="{{$size->id}}" @if($product->size_id == $size->id) selected="selected" @endif>{{$size->height}} x {{$size->width}}</option>
                  @endforeach
               </select> --}}
               
                  <input disabled class="form-control" value="{{$product->size_height_width}}">
               <span class="validation-msg" ></span>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Rate</label>
               <div class="input-group">
                  <input disabled type="number" min="1" class="form-control" error-text="Enter rate.." placeholder="Rate" name="rate" value="{{$product->rate}}">
               </div>
               <!-- input-group -->
               <span class="validation-msg" ></span>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Number of stitches</label>
               <div class="input-group">
                  <input disabled type="number" min="1" class="form-control" error-text="Enter number of stitches.." placeholder="Number of stitches" name="number_of_stitches" value="{{$product->number_of_stitches}}">
               </div>
               <!-- input-group -->
               <span class="validation-msg" ></span>
            </div>
         </div>
      </div>
   </div>
   </div>
   </div>
   <div class="col-lg-6">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="col-md-9">
               <div class="checkbox my-2">
                  <div class="custom-control custom-checkbox">
                     <input disabled type="checkbox" @if(!empty($product->welted_edges_color_id)) checked @endif class="custom-control-input" name="is_welted_edges_color" id="is_welted_edges_color" data-parsley-multiple="groups" data-parsley-mincheck="2">
                     <label class="custom-control-label" for="is_welted_edges_color">Welted Edges?</label>
                  </div>
               </div>
            </div>
            @if(!empty($product->welted_edges_color_id))
            <div class="col-md-4" id="welted-edges-color-section">
               <div class="form-group">
                  <label>Color</label>
                  <div class="input-group">
               
                  {{-- <select disabled  verify-ignore="true" error-text="Select welted edges color" name="welted_edges_color" class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                     <option></option>
                     @foreach($welted_edges_colors as $color)
                     <option @if($product->welted_edges_color_id == $color->id) selected="selected" @endif value="{{$color->id}}">{{$color->name}}</option>
                     @endforeach
                  </select> --}}
               
                  <input disabled type="text"  class="form-control" value="{{$product->welted_edges_color_name}}">
                  </div>
               </div>
            </div>
            @endif
         </div>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="col-md-4">
               <div class="form-group">
                  <label>Thread color</label>
                  {{-- <select disabled verify-ignore="@if(count($product->thread_colors) < 1 ) false @else true @endif" error-text="Select thread color" name="thread_color" multiple required class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                     <option></option>
                     @foreach($thread_colors as $id => $color)
                     <option data-background="{{$color->background}}" data-color="{{$color->color}}" value="{{$color->id}}">{{$color->name}}</option>
                     @endforeach
                  </select> --}}
               </div>
            </div>
            <div class="col-md-12" id="thread_selected_colors">
               @foreach($product->thread_colors as $color)
               <div class="input-group input-group input-group-sm mt-3 thread-color" >
                  <div class="input-group-prepend" >
                     <span class="input-group-text" style="background:{{$color->background}};color:{{$color->color}};">{{$color->name}} ({{$color->color_code}})</span>
                  </div>
                  <input disabled type="text" for="{{$color->name}}" verify-ignore="true" class="form-control" placeholder="No description for {{$color->name}}" name="thread_color_description[{{$color->id}}]"  value="{{$color->pivot->description}}">
                        
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-12">







      <div class="card m-b-30">
         <div class="card-body">
            <div class="row form-material">
               
               
               @can('product-download-emb')
             @php
              $embs = $product->embs->count();  
            @endphp
            
               @if( $embs > 0)
               <div class="col-md-2">
                  <div class="form-group">
                     <label>{{trans('Emb Files')}}</strong> </label>
                     <div class="input-group">
                    <a file-code="{{$product->id}}" get="emb" href="{{route('download.index')}}" class="btn btn-info btn-block btn-md file_download"><i class="dripicons-download"></i> Download</a>
                   @php
                   $file_size=0;
                     foreach($product->embs->get() as $file){
                        $file_size += Storage::size($file->link);
                     }
                  $file_size = number_format($file_size / 1048576,2);

                  @endphp
                  <span> File size : {{$file_size}} MB</span>
                  
                  
                  </div>
               </div>
            </div>
            @endif
            @endcan
            
            @can('product-download-dst') 
             @php
              $dsts = $product->dsts->count();  
            @endphp
   @if( $dsts > 0)
               <div class="col-md-2">
                  <div class="form-group">
                     <label>{{trans('Dst Files')}}</strong> </label>
                     <div class="input-group">
                    <a file-code="{{$product->id}}" get="dst" href="{{route('download.index')}}" class="btn btn-info btn-block btn-md file_download"><i class="dripicons-download"></i> Download</a>
                   @php
                   $file_size=0;
                     foreach($product->dsts->get() as $file){
                        $file_size += Storage::size($file->link);
                     }
                  $file_size = number_format($file_size / 1048576,2);

                  @endphp
                  <span> File size : {{$file_size}} MB</span>
                  
                  </div>
               </div>
            </div>
            @endif
            @endcan

            
            @can('product-download-psd') 
               @php
               $psds = $product->psds->count();  
               @endphp

                @if($psds > 0)
               <div class="col-md-2">
                  <div class="form-group">
                     <label>{{trans('Psd Files')}}</strong> </label>
                     <div class="input-group">
                    <a file-code="{{$product->id}}" get="psd" href="{{route('download.index')}}" class="btn btn-info btn-block btn-md file_download"><i class="dripicons-download"></i> Download</a>
                   @php
                   $file_size=0;
                     foreach($product->psds->get() as $file){
                        $file_size += Storage::size($file->link);
                     }
                  $file_size = number_format($file_size / 1048576,2);

                  @endphp
                  <span> File size : {{$file_size}} MB</span>
                  
                  </div>
               </div>
            </div>
            @endif
            @endcan


@can('product-download-barcode')
             @php
              $barcodes = $product->barcodes->count();  
            @endphp
            
            
            @if($barcodes > 0)
               <div class="col-md-2">
                  <div class="form-group">
                     <label>{{trans('Barcode Files')}}</strong> </label>
                     <div class="input-group">
                    <a file-code="{{$product->id}}" get="barcode" href="{{route('download.index')}}" class="btn btn-info btn-block btn-md file_download"><i class="dripicons-download"></i> Download</a>
                   @php
                   $file_size=0;
                     foreach($product->barcodes->get() as $file){
                        $file_size += Storage::size($file->link);
                     }
                  $file_size = number_format($file_size / 1048576,2);

                  @endphp
                  <span> File size : {{$file_size}} MB</span>
                  
                  </div>
               </div>
            </div>
            @endif
            @endcan
            
            @php
              $images = $product->images->get()->toArray();  
            @endphp

            @if(count($images) > 0)
               <div class="col-md-4">
                  <div class="form-group">
                     <label>{{trans('Images')}} </strong> </label>
                     <div class="input-group">
                        
                        @foreach($images as $id=>$image)
                                    <a class="elem" href="{{Storage::url($images[$id]['link'])}}"  data-lcl-thumb="{{Storage::url($images[$id]['link'])}}">
                     <img height="50" width="50" src="{{Storage::url($images[$id]['link'])}}">
                  </a>
        @endforeach
   		
                  </div>
                  
                  <div class="col-md-6 " style="margin-top:20px;margin-left:-10px">
                  @can('product-download-image')
                  <div class="form-group">
                        <a file-code="{{$product->id}}" get="image" href="{{route('download.index')}}" class="btn btn-info btn-block btn-md file_download"><i class="dripicons-download"></i> Download</a>
                   @php
                   $file_size=0;
                     foreach($images as $file){
                        $file_size += Storage::size($file['link']);
                     }
                  $file_size = number_format($file_size / 1048576,2);

                  @endphp
                  <span> File size : {{$file_size}} MB</span>
                  
                     </div>
                  @endcan
               </div>
               </div>
            </div>
            @endif



       
</div>
            
         </div>
      </div>
   </div>
   </div>
   </div>
</form>


@endsection

@section('script')


lc_lightbox('.elem', {
		wrap_class: 'lcl_fade_oc',
		gallery : true,	
		thumb_attr: 'data-lcl-thumb', 
		
		skin: 'minimal',
		radius: 0,
		padding	: 0,
		border_w: 0,
      ol_color    :'#000',
      ol_pattern    :true,
shadow      :true,

      live_elements :true,
global_type   :'image',


      remove_scrollbar:true,
show_title    :false,
show_descr    :false,
show_author   :false,
download    :true,
touchswipe    :true,
rclick_prevent  :true,


	});	

   $("#is-welted-edges-color").prop("checked", false);
   
   <?php 
      if(empty($product->welted_edges_color_id)){
       echo '$("#welted-edges-color-section").hide();';
      }
      
      ?>



//download cad
    $(document).on("click",'.file_download',(function(e){    
        e.preventDefault(); 

      if($(this).attr('get') == 'image' && $('.elem').length > 1){
             $.LoadingOverlay("show",{progress:true,text:'Zipping...',progressResizeFactor:'0.10',textResizeFactor:'0.20',progressFixedPosition:'bottom',progressColor:"#2a3a4a"}); 
               }else{
            $.LoadingOverlay("show",{progress:true,text:'Starting...',progressResizeFactor:'0.10',textResizeFactor:'0.20',progressFixedPosition:'bottom',progressColor:"#2a3a4a"}); 
         }
                 
    counter=setInterval(timer, 1200); 

    download($(this).attr('href'),{
            get:$(this).attr('get'),
            type:'product',
            id:$(this).attr('file-code')
        },true);
        

        }));



@endsection