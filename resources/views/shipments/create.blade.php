@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Issue New Shipment</h4>
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
                           <input type="text" class="form-control" error-text="Enter shipment date..." placeholder="yyyy/mm/dd" name="shipment_date" id="shipment_date">
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
                           <input type="text" verify-ignore="true" class="form-control" error-text="Enter shipment id..." placeholder="Shipment ID" name="shipment_id" value="{{\Carbon\Carbon::now()->timestamp.rand(0,1000)}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  

                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Company Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter company name..." placeholder="Company name" name="company_name">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Note</label>
                        <div class="input-group">
                           <textarea type="text" class="form-control" error-text="Enter note..." placeholder="Note" name="note"></textarea>
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




   
   <div class="row">
      <div class="col-md-3" >
         <h4 class="page-title font-16">Products: </h4>
                        <div class="input-group" id="products_dropdown" @isset($items) style="display:none;" @endisset>
                           <select class="form-control select2 mb-3 custom-select js-states" error-text="Select products..." id="product_selector" placeholder="Select products">
                              <option></option>
                           </select>
                        </div>
      </div>
                        <!-- input-group -->
      <div class="col-lg-12" style="margin-bottom:70px">
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            <p class="italic" style="color:red" id="product_message"></p>
            <table @if(empty($items)) style="display:none" @endif id="products-table" class="table table-bordered product-list">    
            
            <thead>
                           <tr>
                              <th class="dt-center">{{trans('Title')}}</th>
                              <th class="dt-center">{{trans('Item ID')}}</th>
                              <th class="dt-center">{{trans('Category')}}</th>
                              <th class="dt-center">{{trans('Type')}}</th>
                              <th class="dt-center">{{trans('Fabric Type')}}</th>
                              <th class="dt-center">{{trans('Fabric Color')}}</th>
                              <th class="dt-center">{{trans('Size')}}</th>
                              <th class="dt-center">{{trans('Final Stock Quantity')}}</th>
                              <th class="dt-center">{{trans('Issue Shipment Quantity')}}</th>
                              <th class="dt-center"><i class="dripicons-trash"></i></th>
                           </tr>
                        </thead>
                        <tbody>

@if(!empty($items))
                      @php
                      $item_ids = array_keys($items);
                      $final_stocks = \App\Models\FinalStock::whereIn('id',$item_ids)->get();
                           
                      @endphp
   @if($final_stocks)
   @php
    $final_stocks->load('product');  
   @endphp
   @foreach($final_stocks as $final_stock)
   
         @php
          $product = $final_stock->product;   
         if($loop->index % 2){
            $class = 'even';
         }else{
            $class = 'odd';
         }
         @endphp
         <tr class="{{$class}} product-row">
          <td><a target="_blank" href="{{route('products.show',$final_stock->product->id)}}">{{$product->name}}</a></td>
          <td><a target="_blank" href="{{route('products.show',$final_stock->product->id)}}">{{$product->code}}</a></td>
          <td>{{$product->product_category_name}}</td>
          <td>{{$product->product_type_name}}</td>
          <td>{{$product->fabric_type_name}}</td>
          <td>{{$product->fabric_color_name}}</td>
          <td>{{$product->size_height_width}}</td>
   
          <td style="width:13%"><input disabled for="shipment_stock[{{$final_stock->id}}]" type="number"  class="form-control" value="{{$final_stock->received_stitches}}" /></td>
          <td style="width:13%"><input reference-number="shipment_{{$loop->index}}" for="{{$final_stock->id}}" type="number" min="0" max="{{$final_stock->received_stitches}}" class="form-control" name="shipment_stock[{{$final_stock->id}}]" value="{{$items[$final_stock->id]}}" /></td>
          <td><button type="button" class="rbtnDel btn btn-sm btn-danger">X</button></td>
         </tr>
          @endforeach
                  
         @endif
@endif

                        </tbody>
            
            </table>
         </div>
      </div>

      <div class="col-md-12" @if(empty($items)) style="display:none" @endif id="button">
                  <div class="form-group text-center">
                     <input type="button" value="{{trans('Issue Shipment')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>

      </div>
   </div>



</form>
@endsection

@section('script')



   $('#challan-btn').on('click',function(e){
    e.preventDefault();
    $('#challan_modal').modal('show');
   })
   
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
   
   
   $('[data-toggle="tooltip"]').tooltip(); 
   
   
   $('#product_selector').select2({
     ajax: {
       url: "{{route('relations.final.search')}}",
       dataType: 'json',
       delay: 100,
       method:'POST',
       data: function (params) {
         return {
           q: params.term, // search term
           page: params.page || 1
         };
       },
    
       cache: true
     },
   
    width: '100%',
    minimumResultsForSearch: Infinity,
    placeholder: 'Select product',
     minimumInputLength: 1,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection,
    
   });
   
   
    $('#product_selector').on('select2:select',function(e){
           var flag=true;
                       
                       if ($('input[name="shipment_stock['+e.params.data.position+']"]').length > 0) {
                           swal(
                              'Duplicate',
                              'Duplicate entry not allowed.',
                              'error'
                           );
                           flag = false;
                       }
           
           
           
            $(this).val(null).trigger('change');
   
   
           if(flag){
               append_row(e.params.data);
               if($("#products-table .product-row").length > 0){
                   $("#products-table").show(300);
                   $("#button").show(300);
               }
           }
   
   
       });
   
   
   function formatRepo (repo) {
   if (repo.loading) {
    return repo.text;
   }
   
   
   
   var $container = $(
    "<div class='select2-result-repository clearfix'>" +
      "<div class='select2-result-repository__meta'>" +
         "<div class='select2-result-repository__title'>"+repo.code+" - "+repo.name+ ' - '+ repo.fabric_color+' - '+repo.size+"</div>" +
      //   "<div class='select2-result-repository__description'>Code: "+repo.code+"</div>" +
        "</div>" +
      "</div>" +
    "</div>"
   );
   
   return $container;
   }
   
   
   
   $('#save-challan').click(function(e){
      e.preventDefault();
      
        if(!challan_validate()){
            $("#challan_modal").modal('hide');
        }
        validate(false);
   });

   function formatRepoSelection (repo) {
   return repo.name || repo.text;
   }
   
   function isOdd(num) { return num % 2;}

   function append_row(data){
      var number =($("#products-table .product-row").length + 1);
     var clas = 'even';
          if(isOdd(number)){
            clas = 'odd';
         }
    var newRow = $("<tr class='product-row "+clas+"'>");
          var cols = '';
          cols += '<td><a target="_blank" href="{{url('/')}}/products/'+data.product+'">'+data.name+'</a></td>';
          cols += '<td><a target="_blank" href="{{url('/')}}/products/'+data.product+'">'+data.code+'</a></td>';
          cols += '<td>'+data.product_category+'</td>';
          cols += '<td>'+data.product_type+'</td>';
          cols += '<td>'+data.fabric_type+'</td>';
          cols += '<td>'+data.fabric_color+'</td>';
          cols += '<td>'+data.size+'</td>';
   
          cols += '<td style="width:13%"><input disabled for="shipment_stock['+data.position+']" type="number"  class="form-control" value="'+data.final_stock+'" /></td>';
          cols += '<td style="width:13%"><input reference-number="shipment_'+number+'" for="'+data.position+'" type="number" min="0" max="'+data.final_stock_max+'" class="form-control" name="shipment_stock['+data.position+']" value="0" /></td>';
          cols += '<td><button type="button" class="rbtnDel btn btn-sm btn-danger">X</button></td>';
          newRow.append(cols);
          $("table.product-list tbody").append(newRow);
   
           $('.product-row').each(function(index){
                var element =$(this).find('input[reference-number="shipment_'+number+'"]');
                individual_input(element);
            }); 
          
         validate(false);
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
               }
               else{
                  $(this).val(val);
               }
               
               validate(false);
               // validate_with_final_stock(this,false);
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
               }
               else{
                  $(this).val(val);
               }
               validate(false);
            });
            
            $(input).on('focus',function(){
               hideError($(this).parent(),this);
               $("#product_message").html('');
            });
    }
   
      function validate_with_final_stock(element){
            var reference = $(element).attr('name');
            var maximum = $('input[for="'+reference+'"]').val();

            if($(element).val() <= maximum){
                 // validate_with_final_cut_pieces_stock_quantity(element);
            }else{
               // $("#product_message").html(response.message);
            }
      }



        function validate_with_final_cut_pieces_stock_quantity(element,remove=false){
            $.post("{{route('relations.stock_count.final')}}",{unique:$(element).attr('for'),input:$(element).val(),'on':$(element).attr('reference-number'),'remove':remove},function(response){    
                if(!response.success){
                    $(element).val(Math.abs(response.value));
                    $("#product_message").html(response.message);
                }
                if(response.hasOwnProperty('final_stock')){
                   $('input[for="'+$(element).attr("name")+'"]').val(response.final_stock)
                   $(element).attr('max',Math.abs(response.final_stock));
                  }
            })
        }
   
   $("table#products-table tbody").on("click", ".rbtnDel", function(event) {
   
   var input = $(this).parent().prev().find('input');
   $(input).unbind();
   // validate_with_final_cut_pieces_stock_quantity(input,true);
      $(this).closest("tr").remove();
   
      if($('.product-row').length < 1){
          $("#products-table").hide(300);
          $("#button").hide(300);
          $("#products_dropdown").show(300);
      }
        validate(false);
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
                  
                      $.ajax({
                          type:'POST',
                          url:'{{route('shipments.store')}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                             $.LoadingOverlay("hide");
                            if(response.success){
                               swal({
                                             title: 'Shipment issued successfully!',
                                             text: 'redirecting to manage shipments.',
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
      

       if($("#products-table tr").length < 2){
           has_error = true;
           if(show){
              $("#product_message").html('Select atleast one product');
           }
         }

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