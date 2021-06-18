@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$shipment->company_name}} || {{$shipment->shipment_id}}</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
      </div>
   </div>
</div>

<form id="product-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body ">
               
                <div class="row form-group">
                  

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Shipment Date*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter shipment date..." placeholder="yyyy/mm/dd" name="shipment_date" id="shipment_date" value="{{$shipment->shipment_date}}">
                           <div class="input-group-append calender-open bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Shipment ID*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter shipment id..." placeholder="Shipment ID" name="shipment_id" value="{{$shipment->shipment_id}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  

                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Company Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter company name..." placeholder="Company name" name="company_name" value="{{$shipment->company_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Note</label>
                        <div class="input-group">
                           <textarea type="text" class="form-control" error-text="Enter note..." placeholder="Note" name="note" value="{{$shipment->note}}"></textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>

               
               
            </div>
         </div>
      </div>
   </div>


   
                           @php
      
      $products_pre = \App\Models\Shipment::find($shipment->id)->final_stocks()
      ->with(['product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width']);
     
      $products_pre = $products_pre->get();
      
      $pre_ids = $products_pre->pluck('id')->toArray();
      $products_pre = $products_pre->toArray();
      
      
      $products_new =array();
      if(isset($items)){
         $id = $shipment->id;
         $item_ids = array_diff(array_keys($items),$pre_ids);
         $products_new = \App\Models\FinalStock::whereIn('id',$item_ids)->with(['product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width'])
         ->get()->toArray();
      }
      $final_stocks = array_merge($products_pre,$products_new);
         
         @endphp



      <div class="row">
      <div class="col-lg-12" style="margin-bottom:70px">
         
      <h4 class="page-title font-16">Products: </h4>
      @can(;'shipment-add-product')
         <div class="form-group text-left">
                     <input type="button" value="{{trans('Add Products')}}" id="add-btn" class="btn btn-primary">
                  </div>
                  
                  @endcan

      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            <p class="italic" style="color:red" id="product_message"></p>
            <table id="products-table" class="table table-bordered">    
            
            
            
            <thead>
                           <tr>
                              <th class="dt-center">{{trans('Title')}}</th>
                              <th class="dt-center">{{trans('Item ID')}}</th>
                              <th class="dt-center">{{trans('Category')}}</th>
                              <th class="dt-center">{{trans('Type')}}</th>
                              <th class="dt-center">{{trans('Fabric Type')}}</th>
                              <th class="dt-center">{{trans('Fabric Color')}}</th>
                              <th class="dt-center">{{trans('Size')}}</th>
                              <th class="dt-center">{{trans('Issue Shipment Quantity')}}</th>
                              @can('shipment-remove-product')
                              <th class="dt-center"><i class="dripicons-trash"></i></th>
                              @endcan
                           </tr>
                        </thead>
                        <tbody>
                           
                           
   @if($final_stocks)

   @foreach($final_stocks as $final_stock)
   
         @php
         
         $product = $final_stock['product'];

         if($loop->index % 2){
            $class = 'even';
         }else{
            $class = 'odd';
         }
         @endphp
         <tr class="{{$class}} product-row">
          <td>
             @can('product-view')
             <a target="_blank" href="{{route('products.show',$product['id'])}}">{{$product['name']}}</a>
            @else
               {{$product['name']}}
            @endcan
            </td>
          <td>
             @can('prdouct-view')
             <a target="_blank" href="{{route('products.show',$product['id'])}}">{{$product['code']}}</a>
                 
             @else
                 {{$product['code']}}
             @endif
             @endcan
            </td>
          <td>{{$product['product_category']['name']}}</td>
          <td>{{$product['product_type']['name']}}</td>
          <td>{{$product['fabric_type']['name']}}</td>
          <td>{{$product['fabric_color']['name']}}</td>
          <td>{{$product['size']['height']}} x {{$product['size']['width']}}</td>
   
          @php
           
           if(array_key_exists('pivot',$final_stock)){

              $issue = $value = $final_stock['pivot']['issued_quantity'];
              
              if(!empty($items) && array_key_exists($final_stock['id'],$items)){
                 $issue = $value+$items[$final_stock['id']];
              }
           }else{
              $value = $items[$final_stock['id']];
           }
           $t = 0;
           if(array_key_exists('log_received_stitches',$final_stock) && array_key_exists('log_received_damage',$final_stock)){
              $t = $final_stock['log_received_stitches'] + $final_stock['log_received_damage'];
           }
          @endphp
          <td style="width:13%"><input @cannot('shipment-edit-product') disbaled @endcannot @if($t > 0) disabled @endif reference-number="stitches_{{$loop->index}}" for="{{$final_stock['id']}}" type="number" min="0" class="form-control" name="shipment_stock[{{$final_stock['id']}}]" value="{{$issue}}" /></td>
          
         @can('shipment-remove-product')
            @if($t == 0)
            <td><button type="button" class="rbtnDel btn btn-sm btn-danger">X</button></td>
            @else
            <td></td>
            @endif
         @endcan

         </tr>
          @endforeach
          
@endif
            
            
            </table>
         </div>
      </div>
      
      
               <div class="col-md-12" id="button">
                  <div class="form-group text-center">
                     <input type="button" value="{{trans('Update Shipment')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>

      </div>
   </div>



</form>
@endsection

@section('script')
@can('shipment-add-product')
var add_clicked = false;
   $('#add-btn').on('click',function(e){
      e.preventDefault();
      add_clicked = true;
      $('#submit-btn').click();
   });
@endcan
@can('shipment-remove-product')
$("table#products-table tbody").on("click", ".rbtnDel", function(event) {
   
   var selectOn = $(this);
   var input = $(this).parent().prev().find('input');

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
               // validate_with_stock_quantity(input,true);
      $(input).unbind();
      $(selectOn).closest("tr").remove();
      if($('.product-row').length < 1){
          $("#products-table").hide(300);
          $("#button").hide(300);
          $("#products_dropdown").show(300);
      }
         validate(false);
           

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    
                }
            });
        
      
   });

   
@endcan

   $('#shipment_date').bootstrapMaterialDatePicker({
      weekStart : 0, 
      time: false 
   }).on('dateSelected',function(e,data){
       $('.dtp-btn-ok').click();
   });
   
   $('.calender-open').click(
   function(){
           $('#shipment_date').focus();
   });
   
   
   //submit
   $('#submit-btn').on("click", function (e) {
              e.preventDefault();
              if (!validate()) {
                 $.LoadingOverlay("show");
                      var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#product-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                  
                     formData.append('add_clicked',add_clicked);
                     
                     $.ajax({
                          type:'POST',
                          url:'{{route('shipments.update',$shipment->id)}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                             $.LoadingOverlay("hide");
                            if(response.success){
                               swal({
                                             title: response.title,
                                             text: response.subtitle,
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
                            }else{
                                $(response.element).focus();
                                $("#product_message").html(response.message);
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
             
   //validation part
   function validate(show = true) {
   
      //errors
      var has_error = false;

      $("input[type='text']").each(function(){
          if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  &&  $(this).val() == ''){
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
               $("#product_message").html('Enter issue final amount.');
            }
         }

         if($(this).val() == 0){
            has_error = true;
            if(show){
               showError($(this).parent().parent(),this);
               $("#product_message").html('Enter issue final amount greater than 0');
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
   


      var submit_button_selector = $("#submit-btn");
   
      init_listeners();

     function init_listeners(){
            hideButton();

            $("textarea").change(function(){
               validate(false);
            });

            $("textarea").keyup(function(){
               validate(false);
            });
            
            $("input").each(function(){

               if($(this).attr('type') == 'number'){
                  if($(this).attr('reference-number') != '' ){
                      individual_input($(this));
                     }
                  return;
               }
               
               $(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent());
                   }
                   $('#product_message').html('');
               });

               $(this).change(function() {
                  validate(false);
               });

               $(this).keyup(function() {
                  validate(false);
               });
            });
       }

       
    function individual_input(input){
          $(input).on('change', function (e) {
             var val = parseInt($(this).val(),10);
             var max = parseInt($(this).attr('max'));
             var min = parseInt($(this).attr('min'));
             var  r = 0;
             if(val > max){
                $(this).val(max);
               }else if(val < min){
                  $(this).val(min);
               }else if(isNaN(val)){
                  $(this).val(r);
               }else{
                  $(this).val(val);
               }
               validate(false);
        });
        $(input).on('keyup',function(){
           var val = parseInt($(this).val(),10);
             var max = parseInt($(this).attr('max'));
             var min = parseInt($(this).attr('min'));
             var  r = 0;
             if(val > max){
                $(this).val(max);
               }else if(val < min){
                  $(this).val(min);
               }else if(isNaN(val)){
                  $(this).val(r);
               }else{
                  $(this).val(val);
               }
            validate(false);
            $("#product_message").html('');
        });

        $(input).on('focus',function(){
            hideError($(this).parent(),this);
            $("#product_message").html('');
        });
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

   
         
@endsection