@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$product->name}} || {{$product->code}}</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
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
                        <label>Item ID*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter item id..." placeholder="Product ID" name="product_id" id="product_id" value="{{$product->code}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Title*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter title..." placeholder="Product name" name="product_name" value="{{$product->name}}">
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
                           <textarea type="text" class="form-control" error-text="Enter product description..." placeholder="Product description" name="product_description">{{$product->description}}</textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               <div class="row form-material">
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Category*</label>
                        @if($product->productions_count <= 0)
                        <select error-text="Select category" relation="product_types" required name="product_categories" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">   
                        @foreach($product_categories as $product_category)
                        <option @if($product->product_category_id == $product_category->id) selected="selected" @endif value="{{$product_category->id}}" is-relation="@php if(count($product_category->types) > 0) echo true; @endphp">{{$product_category->name}}</option>
                        @endforeach
                        </select>
                        @else
                        <input disabled value="{{$product->product_category_name}}" class="form-control">
                        @endif
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-2" id="product_types" @if(empty($product->product_type_id)) style="display:none" @endif>
                  @if(!empty($product->product_type_id))
                  <div class="form-group">
                     @php
                     $product_types = \App\Models\ProductCategory::find($product->product_category_id)->types()->get();
                     @endphp
                     <label>Product Type*</label>
                     @if($product->productions_count <= 0)
                     <select name="product_types" relation="fabric_types" required class="form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                     @foreach($product_types as $product_type)
                     @php $text= $product_type->fabric_types()->count() > 0 ? 1 : ''; @endphp
                     <option @if($product->product_type_id == $product_type->id) selected="selected" @endif value="{{$product_type->id}}" is-relation="{{$text}}" >{{$product_type->name}}</option>
                     @endforeach
                     </select>
                     @else
                     <input disabled value="{{$product->product_type_name}}" class="form-control">
                     @endif
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
                  <label>Fabric Type *</strong> </label>
                  @if($product->productions_count <= 0)
                  <select name="fabric_types" relation="fabric_colors" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Type...">
                  @foreach($fabric_types as $fabric_type)
                  @php $text= $fabric_type->colors()->count() > 0 ? 1 : ''; @endphp
                  <option @if($product->fabric_type_id == $fabric_type->id) selected="selected" @endif value="{{$fabric_type->id}}" is-relation="{{$text}}" >{{$fabric_type->name}}</option>
                  @endforeach
                  </select>
                  @else
                  <input disabled value="{{$product->fabric_type_name}}" class="form-control">
                  @endif
                  <span class="validation-msg" ></span>
               </div>
               @endif
            </div>
            <div class="col-md-2" id="fabric_colors" @if(empty($product->fabric_color_id)) style="display:none" @endif>
            @if(!empty($product->fabric_color_id))
            @php
            $colors = \App\Models\FabricType::find($product->fabric_type_id)->colors()->get();
            @endphp
            <div class="form-group">
               <label>Fabric Color *</strong> </label>
               @if($product->productions_count <= 0)
               <select name="fabric_colors" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Color...">
               @foreach($colors as $color)
               <option @if($product->fabric_color_id == $color->id) selected="selected" @endif value="{{$color->id}}">{{$color->name}}</option>
               @endforeach
               </select>
               @else
               <input disabled value="{{$product->fabric_color_name}}" class="form-control">
               @endif
               <span class="validation-msg" ></span>
            </div>
            @endif
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Size*</label>
               @if($product->productions_count <= 0)
               <select error-text="Select product size" name="size" required class=" form-control" data-live-search="true" data-live-search-style="begins" title="Select Size...">
                  <option></option>
                  @foreach($sizes as $id => $size)  
                  <option value="{{$size->id}}" @if($product->size_id == $size->id) selected="selected" @endif>{{$size->height}} x {{$size->width}}</option>
                  @endforeach
               </select>
               @else
               <input disabled value="{{$product->size_height_width}}" class="form-control">
               @endif
               <span class="validation-msg" ></span>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Rate*</label>
               <div class="input-group">
                  <input type="number" min="1" class="form-control" error-text="Enter rate.." placeholder="Rate" name="rate" value="{{$product->rate}}">
               </div>
               <!-- input-group -->
               <span class="validation-msg" ></span>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label>Number of stitches*</label>
               <div class="input-group">
                  <input type="number" min="1" class="form-control" error-text="Enter number of stitches.." placeholder="Number of stitches" name="number_of_stitches" value="{{$product->number_of_stitches}}">
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
                     <input type="checkbox" @if(!empty($product->welted_edges_color_id)) checked @endif class="custom-control-input" name="is_welted_edges_color" id="is_welted_edges_color" data-parsley-multiple="groups" data-parsley-mincheck="2">
                     <label class="custom-control-label" for="is_welted_edges_color">Welted Edges?</label>
                  </div>
               </div>
            </div>
            <div class="col-md-4" id="welted-edges-color-section">
               <div class="form-group">
                  <label>Color*</label>
                  <select verify-ignore="true" error-text="Select Color" name="welted_edges_color" class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                     <option></option>
                     @foreach($welted_edges_colors as $color)
                     <option @if($product->welted_edges_color_id == $color->id) selected="selected" @endif value="{{$color->id}}">{{$color->name}}</option>
                     @endforeach
                  </select>
                  <span class="validation-msg" ></span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-lg-6">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="col-md-4">
               <div class="form-group">
                  <label>Thread Color*</label>
                  <select verify-ignore="@if(count($product->thread_colors) < 1 ) false @else true @endif" error-text="Select thread color" name="thread_color" multiple required class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                     <option></option>
                     @foreach($thread_colors as $id => $color)
                     <option data-background="{{$color->background}}" data-color="{{$color->color}}" value="{{$color->id}}">{{$color->name}} ({{$color->color_code}})</option>
                     @endforeach
                  </select>
                  <span class="validation-msg" ></span>
               </div>
            </div>
            <div class="col-md-12" id="thread_selected_colors">
               @foreach($product->thread_colors as $color)
               <div class="input-group input-group input-group-sm mt-3 thread-color" >
                  <div class="input-group-prepend" >
                     <span class="input-group-text" style="background:{{$color->background}};color:{{$color->color}};">{{$color->name}} ({{$color->color_code}})</span>
                  </div>
                  <input type="text" for="{{$color->name}} ({{$color->color_code}})" verify-ignore="true" class="form-control" placeholder="Write description for {{$color->name}}" name="thread_color_description[{{$color->id}}]" value="{{$color->pivot->description}}">
                  
                  @if($product->productions_count <= 0)
                  
                  <div class="input-group-append">
                     <button class="btn btn-danger ibtnDel" class="form-control"  type="button">X</button>
                  </div>

                  @endif

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
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach Image files')}} *</strong> </label>
                  <div class="input-images"></div>
               </div>
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach DST files')}} *</strong> </label>
                  <div class="input-dsts"></div>
               </div>
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach EMB files')}} *</strong> </label>
                  <div class="input-embs"></div>
               </div>
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach PSD files')}}</strong> </label>
                  <div class="input-psds"></div>
               </div>
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach Barcode files')}}</strong> </label>
                  <div class="input-barcodes"></div>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="form-group">
               <button type="button" disabled class="btn btn-secondary waves-effect" id="submit-btn">Update</button>
            </div>
         </div>
      </div>
   </div>
   </div>
   </div>
</form>
@endsection

@section('script')
   $("#is-welted-edges-color").prop("checked", false);
     
     <?php 
      if(empty($product->welted_edges_color_id)){
       echo '$("#welted-edges-color-section").hide();';
      }
      
      ?>
     
     $("input[name='is_welted_edges_color']").on("change", function () {
             if ($(this).is(':checked')) {
                 $('select[name="welted_edges_color"]').attr('verify-ignore','false');
                 $("#welted-edges-color-section").show(300);
             }
             else{
                 $("#welted-edges-color-section").hide(300);
                 $('select[name="welted_edges_color"]').attr('verify-ignore','true');
             }
     
     });
     
     
     
     
     
   
           @php         
           $prev_images = $product->images->get();
           $arr = array();
           foreach($prev_images as $prev_image){
              $path_type= $prev_image->path_type;
           $arr[] = array('id'=>$prev_image->id,'src'=>Storage::url($prev_image->link));
           }
   
           @endphp
           
        var preloadedImages = @php echo json_encode($arr); @endphp;   
        
   
        $('.input-images').imageUploader(
           {
           imagesInputName:'image',
           extensions: ['.png','.jpg','.jpeg'],
           mimes: ['image/jpeg', 'image/png'],
           preloadedInputName:'image_files_old',
           preloaded:preloadedImages,
           errorText:'Please select atleast one image file',
           }
        );
        
   
        
           @php
           $prev_dsts = $product->dsts->get();
           $arr = array();
           foreach($prev_dsts as $prev_dst){
              $path_type=$prev_dst->path_type;
           $arr[] = array('id'=>$prev_dst->id,'src'=>"data:image/webp;base64,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");
           }
   
           @endphp
           
        var preloadedDsts = @php echo json_encode($arr); @endphp;   
        
        $('.input-dsts').imageUploader(
           {
           imagesInputName:'dst',
           extensions: ['.dst'],
           mimes: [""],
           preloadedInputName:'dst_files_old',
           preloaded:preloadedDsts,
           errorText:'Please select atleast one dst file',
           
        }
        );
        
           @php
           $prev_barcodes = $product->barcodes->get();
           $arr = array();
           foreach($prev_barcodes as $prev_barcode){
              $path_type=$prev_barcode->path_type;
           $arr[] = array('id'=>$prev_barcode->id,'src'=>"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAAkFBMVEUAAADi5efi5efi5efi5efAxsvi5efi5efi5efi5edXbX5XbX5XbX5XbX7////////i5eewt721u8G/xcrJztLb3+Gyub/N0dW+xMnW29/K0dhXbX5leYiIl6PU2dxid4dvgZCgq7Vrfo7L0tjM09rS2N7M09ng4+X////q7O7V2t7AyM6BkZ5sf46Wo66rtr7IaeHLAAAAEHRSTlMAJH6z58EAbvW1Gqfn6wAApKU7ZgAAAwtJREFUeJzt23tTnDAQAPB71Tu1ndKqtYq2WrUlPL//tysJAQJ5DmE390d2xplcYNxfkg03g2bz1RDb3f6QLIxPG8fQp784npZmb+Obq0Cf/9IjfQtwFWgBR6/8LcBRoF1/n/nvAG4CHWDnl58BnAQ6wH4NgItAB1i8/yYAB4EO4Jm/B9gF0ACrABxgE8ADLAIEgFmAATAKUAAmAQ7AIEAC6AVYAK0ADaAT4AE0AkSAWoAJUApQASoBLkAhgAJ8dxVAAW40AEkABbjVAeYCKMDdD0cBFCC51wKmAjBA8tNNAAdI7p1WARCQ3N3e2HcjJIDGw6M60AA6AR5AI0AEqAWYAKUAFaAS4AIUAmSALMAGSAJ0wFyAD5gJAgCmghCAiSAIQBSEAQiCQIBREAowCIIBekE4ABcEBHSCkAAmCAqggrCAVhAYkDyEBiQRcIaAq+vPKVA8Pf+yA66+QKWn8VsSSIBryPxp+mwFgM1/F09WAGz+NI2ACIiACIiAhYCMBcmLUvjIo8yyfHZ7k2UNBIBGXs0BVfszuz1vWVCArJgDUtIi6E1UUve3V6sDaIayaNdh2teNl+WtOY9KiOKX+ANYQwY0PG+R8au1XBUrzkAhAWqel7CS7CS6GvSvgULs6xq8CtvdQLrMpK8FEEAl9PEWYQNv+h1ZGWpwhV1AqrGPt3I2cNIuBNsQpaEGPYuwqsm4CCOgoQOv6JWGTn4jLtS6gFQc3QgoaZPlZmuQG0rAG6Bs0WZJ2Ef+ZNSWAMwM0DHn3d4vWEtfAr7PgSZT1QCdfv4Yrmd7dUXA+OUjA6rxwkCBApBS6Buu04dg9/TNRePqAFLUk76hXQzDrjPDF8FywHoRAREQAREQAecPAH5R+WIFAL+qfbUCYF9W/3mzAiBf17+8vj3aAbOQ/u3FL95DAz4CA/7+Cwp4/5Dyn+MfrSIgAiLg3AC+Bx4tcbACfI98WmJvBfgeerXEzgrwPfZrjtPWCvA9+GyO48YO8Dz6bYzLCweA5+F3Q5yOQn4DwO/4vy4O+91WSL/5DwjcjfgWF+N2AAAAAElFTkSuQmCC");
           }
   
           @endphp
           
        var preloadedBarcodes = @php echo json_encode($arr); @endphp;   
        
        $('.input-barcodes').imageUploader(
           {
              imagesInputName:'barcode',
              extensions: ['.btw'],
              mimes: [""],
              preloadedInputName:'barcode_files_old',
              preloaded:preloadedBarcodes,
              verifyIgnore:true,
       
           }
           );
   
   
            @php
           $prev_psds = $product->psds->get();
           $arr = array();
           foreach($prev_psds as $prev_psd){
              $path_type=$prev_psd->path_type;
           $arr[] = array('id'=>$prev_psd->id,'src'=>"data:image/png;base64,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");
           }
   
           @endphp
           
        var preloadedPsds = @php echo json_encode($arr); @endphp;   
        
           $('.input-psds').imageUploader(
              {
                 imagesInputName:'psd',
                 extensions: ['.psd'],
                 mimes: [""],
                 preloadedInputName:'psd_files_old',
                 preloaded:preloadedPsds,
                 verifyIgnore:true,
              });
              
   
   
               @php
           $prev_embs = $product->embs->get();
           $arr = array();
           foreach($prev_embs as $prev_emb){
              $path_type=$prev_emb->path_type;
           $arr[] = array('id'=>$prev_emb->id,'src'=>"data:image/webp;base64,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");
           }
   
           @endphp
           
        var preloadedEmbs = @php echo json_encode($arr); @endphp;   
        
              $('.input-embs').imageUploader(
                 {
                    imagesInputName:'emb',
                    extensions: ['.emb'],
                    mimes: [""],
                 preloadedInputName:'emb_files_old',
                 preloaded:preloadedEmbs,
                 
           errorText:'Please select atleast one emb file',
                 }
              );
   
   
   
   
               $("#submit-btn").on("click", function (e) {
                     e.preventDefault();
                     if (!validate()) {
                        
                             var formData = new FormData();
                             var data = $("#product-form").serializeArray();
                             $.each(data, function (key, el) {
                                 formData.append(el.name, el.value);
                             });
                             
                             var cad_emb = document.getElementById('emb_files');
                             
                             $.each(cad_emb.files,function(key,el){
                              formData.append('emb_files[]',el);
                             });
                             
                             
                             var cad_dst = document.getElementById('dst_files');
                             
                             $.each(cad_dst.files,function(key,el){
                              formData.append('dst_files[]',el);
                             });
                             
                             var barcode = document.getElementById('barcode_files');
   
                             $.each(barcode.files,function(key,el){
                              formData.append('barcode_files[]',el);
                             });
                             
                             var image = document.getElementById('image_files');
                             
                             $.each(image.files,function(key,el){
                              formData.append('image_files[]',el);
                             });
                             
                             var psd = document.getElementById('psd_files');
                             
                             $.each(psd.files,function(key,el){
                              formData.append('psd_files[]',el);
                             });
                             
   
                             if(psd.files.length > 0 || image.files.length > 0 || barcode.files.length > 0 || cad_emb.files.length > 0 || cad_dst.files.length > 0){ 
                                 $.LoadingOverlay("show",{progress:true,text:'Starting upload...',progressResizeFactor:'0.10',textResizeFactor:'0.20',progressFixedPosition:'bottom',progressColor:"#2a3a4a"}); 
                            }else{
                             $.LoadingOverlay("show"); 
                             }
                             
                           var cheked = 'no';
                           if($("input[name='is_welted_edges_color']").is(':checked')){
                              cheked = 'yes';
                           }
                           formData.append('is_welted_edges_color',cheked);
                       
                             $.ajax({
                                 type:'POST',
                                 url:'{{route('products.update',$product->id)}}',
                                 data: formData,
                                 processData: false,
                                 contentType: false,
                                xhr: function() {
                                   var xhr = new window.XMLHttpRequest();
                                   if(psd.files.length > 0 || image.files.length > 0 || barcode.files.length > 0 || cad_emb.files.length > 0 || cad_dst.files.length > 0){
   
                                      xhr.upload.addEventListener("progress", function(evt) {
                                         
                                         if (evt.lengthComputable) {
                                            
                                            var percentComplete = (evt.loaded / evt.total) * 100;
                                            $.LoadingOverlay("text", Math.round(percentComplete)+"% uploaded");
                                            $.LoadingOverlay("progress",Math.round(percentComplete));
                                         }
                                      }, false);
                                   }
                                   return xhr;
                                   },
                                   success:function(response){
                                    $.LoadingOverlay("hide");
                                    if(response.success){
                                       
                                      swal({
                                         title: 'Product updated successfully!',
                                               text: 'redirecting to manage products.',
                                               type: 'success',
                                               showCancelButton: false,
                                               confirmButtonClass: 'btn btn-success',
                                               cancelButtonClass: 'btn btn-danger m-l-10',
                                               timer: 1000
                                            }).then(
                                            function(){
   
                                            }, 
                                            function (dismiss) {
                                                  if (dismiss === 'timer') {
                                                        location.href = response.redirect;
                                                  }
                                      });
                                    }
                                 },error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
                             });
                         
                     }
                 });
   
   
   
                 
         $('select[name="thread_color"]').on('select2:select',function(e){
             var flag=true;
             $(".thread-color").each(function() {
                 var element = $(this).find('input'); 
                         if ($(element).attr('for') == e.params.data.text) {
                            swal(
                                'Duplicate',
                                'Duplicate entry not allowed.',
                                'error'
                             );
                             flag = false;
                         }
             });
             $(this).val(null).trigger('change');
             if(flag){
                 append_selected_colors(e.params.data);
                 if($("#thread_selected_colors .thread-color").length > 0){
                     $("#thread_selected_colors").show(300);
                 }
             }
         });
     
         function append_selected_colors(data){
             var row_data = '<div class="input-group input-group input-group-sm mt-3 thread-color" >'
                                                                     +'<div class="input-group-prepend" >'
                                                                         +'<span class="input-group-text" style="background:'+data.element.dataset.background+';color:'+data.element.dataset.color+';">'+data.text+'</span>'
                                                                     +'</div>'
                                                                     +'<input type="text" for="'+data.text+'" verify-ignore="true" class="form-control" placeholder="Write description for '+data.text+'" name="thread_color_description['+data.id+']">'
                                                                     +'<div class="input-group-append">'
                                                                         +'<button class="btn btn-danger ibtnDel" class="form-control"  type="button">X</button>'
                                                                     +'</div>'
                                                                 +'</div>';
             $('#thread_selected_colors').append(row_data);
            
           validate(false);
         }
     
     
          $("#thread_selected_colors").on("click", ".ibtnDel", function(event) {
            var selectOn = $(this);
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                buttonsStyling: false
            }).then(function () {
               
             $(selectOn).parent().parent().remove();
             validate(false);

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    
                }
            });
                
         });
        
        
        
        $('[data-toggle="tooltip"]').tooltip();
              function select_listner(id){
               $("select[name="+id+"]").change(function(){
                   var relation  = $("select[name="+id+"]").attr('relation');
                   var relation_avail = $('option:selected',this).attr('is-relation');
                    $('#'+relation).hide(300);
                       nestedRelationHide(relation);
                   if(relation_avail == 1){
                       fetchAndAppendOptions(relation,$(this).val());   
                    }
                    
                  validate(false);
               });
           }
        
           function nestedRelationHide(id){
               if($('#'+id).length > 0){
                   var relation = $('#'+id).find('select').attr('relation');
                   $('#'+id).hide(300);
                   $('#'+id).html('');
                   nestedRelationHide(relation);
               }
              }
              
              function fetchAndAppendOptions(element,id){
                 $.post("<?php echo route('relations.switch')?>",{for:element,id:id},function(result,status){
                    
                    if(status){
                       $('#'+element).show(400);
                       $("#"+element).html(result);
                       select_listner(element);
                       $("select[name="+element+"]").select2({
                          width: '100%',
                          minimumResultsForSearch: Infinity,
                          placeholder: $("select[name="+element+"]").attr('error-text'), 
                        });      
                     }else{
                        $('#'+element).hide(300);
                     }
                  validate(false);
                     
           });
           }
        
        
        
        
        //validation part
        function validate(show = true) {
           //errors
   
           console.log('runs');
           var has_error = false;
           
          
           $("input[type='file']").each(function(){
              var id = $(this).attr('id');
             if($(this).attr('verify-ignore') != 'true' && $(this).attr('multiple') == 'multiple' && $(this).val() == '' && $("input[for='"+id+"_old']").length <= 0){
                 if(show){
                    showError($(this).parent().parent(),this);
                 }
                 has_error = true;
              }
          });
     
   
           $("input[type='text']").each(function(){
              if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  && $(this).val() == ''){
                 has_error = true;
                 if(show){
                    showError($(this).parent().parent(),this);        
                 }
              }
           });
           
           $("input[type='number']").each(function(){
              if($(this).val() == ''){
                 has_error = true;
                 if(show){
                    showError($(this).parent().parent(),this);
                 }
              }
              if($(this).val() == 0){
                 has_error = true;
                 if(show){
                    showError($(this).parent().parent(),this);
                 }
               }
           });
           
           if($('.thread-color').length < 1){
              $("#thread_selected_colors").hide(300);
              $('select[name="thread_color"]').attr('verify-ignore','false');
           }else{
              $('select[name="thread_color"]').attr('verify-ignore','true');
           }
           
           $('select').each(function() {
              if($(this).attr('verify-ignore') != 'true'){
                 if($(this).val() == ''){
                    if(show){
                       showError($(this).parent(),this);
                    }
                    has_error = true;
                 }
              }
           });
       
           if(!has_error){
              showButton();
           }else{
              hideButton();
           }
   
           return has_error;   
        }
   
   
   
              
        var submit_button_selector = $("#submit-btn");
        init_listeners();
        function init_listeners(){
              
   
   
            $("textarea").change(function(){
               validate(false);
            });

            $("textarea").keyup(function(){
               validate(false);
            });
            
   
      select_listner('product_categories');
     select_listner('product_types');
     select_listner('fabric_types');
     select_listner('fabric_colors');
     
   
   
   
           hideButton();
   
           $("select").each(function(){
               $(this).change(function() {
                  if($(this).attr('verify-ignore') != 'true' ){
                     validate(false);
                     hideError($(this).parent(),this);
                  }
                 });
   
                 $(this).select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                placeholder: $(this).attr('error-text'), 
                 });
   
           });
   
   
           $(document).on('click','.swal2-confirm',function(){
              validate(false);
           });
   
           $(document).on('click','.ibtnDel',function(){
              validate(false);
           });
   
   
           $("input").each(function(){
              $(this).change(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                 });
                 $(this).keyup(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                });
           });
           
           $('input[name="number_of_stitches"]').keyup(function(){
              calculate_rate($(this).val());
           });

           $('input[name="number_of_stitches"]').change(function(){
              calculate_rate($(this).val());
           });  
           
           $('input[name="rate"]').keyup(function(){
              calculate_stitches($(this).val());
           });

           $('input[name="rate"]').change(function(){
              calculate_stitches($(this).val());
           });

        }

        function calculate_rate(val){
           var rate = Math.abs((val/1000)*2).toFixed(2);

           $('input[name="rate"]').val(toFixed(rate));
        }

        function calculate_stitches(val){
           var number_of_stitches = Math.abs((val/2)*1000);
           $('input[name="number_of_stitches"]').val(toFixed(number_of_stitches));
        }

   
        
        function hideButton(){
           submit_button_selector.prop('disabled',true);
           submit_button_selector.removeClass('btn-info');
           submit_button_selector.addClass('btn-primary');
        }
   
        function showButton(){
           submit_button_selector.prop('disabled',false);
           submit_button_selector.removeClass('btn-primary');
           submit_button_selector.addClass('btn-info');
        }
        
        
        //error hide
        function hideError(element,main=null){
           if(!main){
               main = element;
           }
           $(element).removeClass('has-error');
           var message_box = $(element).find('.validation-msg');
           $(message_box).html('');
        }
        //error show
        function showError(element,main=null){
           if(!main){
               main = element;
           }
           $(element).addClass('has-error');
           var message_box = $(element).find('.validation-msg');
           $(message_box).html($(main).attr('error-text'));
        }
   
     
@endsection