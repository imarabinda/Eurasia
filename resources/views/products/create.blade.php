@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Create New Product</h4>
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
                           <input type="text" class="form-control" error-text="Enter item id..." placeholder="Product ID" id="product_id" name="product_id">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Title*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter title..." placeholder="Product name" name="product_name">
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
                           <textarea type="text" class="form-control" error-text="Enter product description..." placeholder="Product description" name="product_description"></textarea>
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
                        <select error-text="Select category" relation="product_types" required name="product_categories" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option></option>
                           @foreach($product_categories as $product_category)
                           <option value="{{$product_category->id}}" is-relation="@php if(count($product_category->types) > 0) echo true; @endphp">{{$product_category->name}}</option>
                           @endforeach
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-2" id="product_types" style="display: none">
                     <!--types will be added-->    
                  </div>
                  <div class="col-md-2" id="fabric_types" style="display: none">
                     <!--fabric types will be added-->  
                  </div>
                  <div class="col-md-2" id="fabric_colors" style="display: none">
                     <!--fabric colors will be added-->
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Size*</label>
                        <select error-text="Select size" required name="size" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option></option>
                           @foreach($sizes as $size)
                           <option value="{{$size->id}}" >{{$size->height}} x {{$size->width}}</option>
                           @endforeach
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Rate*</label>
                        <div class="input-group">
                           <input type="number" min="0" class="form-control" error-text="Enter rate.." placeholder="Rate" name="rate" value="">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Number of stitches*</label>
                        <div class="input-group">
                           <input type="number" min="0" class="form-control" error-text="Enter number of stitches.." placeholder="Number of stitches" name="number_of_stitches" value="">
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
                        <input type="checkbox" class="custom-control-input" name="is_welted_edges_color" id="is_welted_edges_color" data-parsley-multiple="groups" data-parsley-mincheck="2">
                        <label class="custom-control-label" for="is_welted_edges_color">Welted Edges?</label>
                     </div>
                  </div>
               </div>
               <div class="col-md-4" id="welted-edges-color-section">
                  <div class="form-group">
                     <label>Color*</label>
                     <select verify-ignore="true" error-text="Select Color" name="welted_edges_color" class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                        <option></option>
                        @foreach($welted_edges_colors as $id => $color)
                        <option value="{{$color->id}}">{{$color->name}}</option>
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
                     <select verify-ignore="false" error-text="Select thread color" name="thread_color" multiple required class="select2 form-control mb-3 custom-select js-states form-control" data-live-search="true" data-live-search-style="begins" title="Select Welted edges color...">
                        <option></option>
                        @foreach($thread_colors as $id => $color)
                        <option data-background="{{$color->background}}" data-color="{{$color->color}}" value="{{$color->id}}">{{$color->name}} ({{$color->color_code}})</option>
                        @endforeach
                     </select>
                     <span class="validation-msg" ></span>
                  </div>
               </div>
               <div class="col-md-12" id="thread_selected_colors">
                  <!--colors will be added-->                                    
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
                  <label>{{trans('Attach PSD files')}} </strong> </label>
                  <div class="input-psds"></div>
               </div>
               <div class="col-md-4 mt-2">
                  <label>{{trans('Attach Barcode files')}} </strong> </label>
                        <div class="input-barcodes"></div>
               </div>

               </div>

            </div>
            <div class="col-md-4">
               <div class="form-group">
                <button type="button" disabled class="btn btn-secondary waves-effect" id="submit-btn">Create</button>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
</form>
@endsection

@section('script')

      
      $('.input-images').imageUploader(
         {
         imagesInputName:'image',
         extensions: ['.png','.jpg','.jpeg'],
         mimes: ['image/jpeg', 'image/png'],
         
         errorText:'Please select atleast one image file',
         }
      );
      
      $('.input-dsts').imageUploader(
         {
         imagesInputName:'dst',
         extensions: ['.dst'],
         mimes: [""],
         
         errorText:'Please select atleast one dst file',
      }
      );
      
      $('.input-barcodes').imageUploader(
         {
            imagesInputName:'barcode',
            extensions: ['.btw'],
            mimes: [""],
            verifyIgnore:true,
         }
         );
         
         $('.input-psds').imageUploader(
            {
               imagesInputName:'psd',
               extensions: ['.psd'],
               mimes: [""],
            verifyIgnore:true,
            }
            );
            
            $('.input-embs').imageUploader(
               {
                  imagesInputName:'emb',
                  extensions: ['.emb'],
                  mimes: [""],
                  
         errorText:'Please select atleast one emb file',
               }
            );
            
            
            

      $("#submit-btn").on("click", function (e) {
                   e.preventDefault();
                   if (!validate()) {
                            var formData = new FormData();
                           // Append all form inputs to the formData Dropzone will POST
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
                               type:'POST',
                               url:'{{route('products.store')}}',
                               data: formData,
                               processData: false,
                               contentType: false,
                  
                               success:function(response){
                                  
                                 $.LoadingOverlay("hide");
                                if(response.success){
                                    swal({
                                             title: 'Product created successfully!',
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


               
   $("#is-welted-edges-color").prop("checked", false);
   $("#welted-edges-color-section").hide();
   
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
           $(this).parent().parent().remove();
           validate(false);
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
            hideButton();
           $("select").each(function(){
              if($(this).attr('class') == 'swal2-select'){
                return;
            }

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

      //validation part
      function validate(show = true) {
         //errors
         var has_error = false;
         
        $("input[type='file']").each(function(){
           if($(this).attr('verify-ignore') != 'true' && $(this).attr('multiple') == 'multiple' && $(this).val() == ''){
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
                 if(show){showError($(this).parent().parent(),this);
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
                  
            
                   has_error = true;
                   if(show){
                   showError($(this).parent(),this);
               }
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