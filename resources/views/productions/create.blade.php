@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Issue New Embroidery</h4>
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
                  <div class="col-md-12">
                  <h4 class="page-title">Consignee Details</h4>
                  </div> 

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Issue Date*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter issue date..." placeholder="yyyy/mm/dd" name="issue_date" id="issue_date">
                           <div class="input-group-append calender-open bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Vendor Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter vendor name..." placeholder="Enter vendor name" name="vendor_name">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor GST No.</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter vendor gst no..." verify-ignore="true" placeholder="Vendor GST no." name="vendor_gst_no">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor Address</label>
                        <div class="input-group">
                           <textarea type="text" class="form-control" error-text="Enter vendor address..." placeholder="Vendor Address" name="vendor_address"></textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="form-group text-left">
                     <input type="button" value="{{trans('Add Consignor Details')}}" id="challan-btn" class="btn btn-primary">
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
                              <th class="dt-center">{{trans('Issue Embroidery')}}</th>
                              <th class="dt-center"><i class="dripicons-trash"></i></th>
                           </tr>
                        </thead>
                        <tbody>

@if(!empty($items))
                      @php
                      $item_ids = array_keys($items);
                      $products = \App\Models\Product::whereIn('id',$item_ids)->get();
                           
                      @endphp
   @if($products)
   @php
    $products->load('fabric_type','fabric_color','product_type','product_category','size');  
   @endphp
   @foreach($products as $product)
         @php 
         if($loop->index % 2){
            $class = 'even';
         }else{
            $class = 'odd';
         }
         @endphp
         <tr class="{{$class}} product-row">
          <td><a target="_blank" href="{{route('products.show',$product->id)}}">{{$product->name}}</a></td>
          <td><a target="_blank" href="{{route('products.show',$product->id)}}">{{$product->code}}</a></td>
          <td>{{$product->product_category_name}}</td>
          <td>{{$product->product_type_name}}</td>
          <td>{{$product->fabric_type_name}}</td>
          <td>{{$product->fabric_color_name}}</td>
          <td>{{$product->size_height_width}}</td>
   
          <td style="width:13%"><input reference-number="embroidery_{{$loop->index}}" for="{{$product->id}}" type="number" min="0" class="form-control" name="embroidery_stock[{{$product->id}}]" value="{{$items[$product->id]}}" /></td>
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
                     <input type="button" value="{{trans('Issue Embroidery')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>

      </div>
   </div>






   <div class="modal fade bd-example-modal-form" id="challan_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalform">Add Consignor Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                           @php
                             $selects = \Illuminate\Support\Facades\DB::table('productions')->groupBy('consignor_name','consignor_address','consignor_gst_no')->select(['consignor_name','consignor_address','consignor_gst_no'])->get();  
                           @endphp
@if($selects->count() > 0)
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Select previous consignor's</label>
                        <select error-text="Select consignor" class="select2 form-control mb-3 custom-select js-states form-control" id="previous_consignor" style="width: 100%; height:36px;">
                           <option></option>
                           
                           @foreach($selects as $select)
                           <option value="{{json_encode($select)}}">{{$select->consignor_name}}</option>
                           @endforeach
                           
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  @endif


                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="field-1" class="control-label">Name*</label>
                        <input type="text" verify-ignore="true" error-text="Enter consignore name.." class="form-control" name="consignor_name" placeholder="Name">
                     </div>
                     <span class="validation-msg" ></span>
                  </div>
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="field-1" class="control-label">Address</label>
                        <div class="input-group"><input error-text="Enter consignore address.." type="text" class="form-control" verify-ignore="true" name="consignor_address" placeholder="Address">
                        </div>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div class="form-group">
                        <label for="field-1" class="control-label">GST No.</label>
                        <div class="input-group"><input type="text" verify-ignore="true" class="form-control" name="consignor_gst_no" placeholder="GST No.">
                        </div>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">                                                             
               <button type="button" class="btn btn-primary" id="save-challan">Save</button>
            </div>
         </div>
      </div>
   </div>
</form>
@endsection

@section('script')



   $("#previous_consignor").select2({
                        width: '100%',
                        minimumResultsForSearch: Infinity,
                        placeholder: $("#previous_consignor").attr('error-text'), 
   });
                     
   $('#previous_consignor').on('select2:select',function(e){
      var json = JSON.parse(e.params.data.id);
      $("input[name='consignor_name']").val(json.consignor_name);
      $("input[name='consignor_address']").val(json.consignor_address);
      $("input[name='consignor_gst_no']").val(json.consignor_gst_no);
      challan_validate(false);
   });


   $('#challan-btn').on('click',function(e){
    e.preventDefault();
    $('#challan_modal').modal('show');
   })
   
   $('#issue_date').bootstrapMaterialDatePicker({
      weekStart : 0, 
      time: false 
   }).on('dateSelected',function(e,data){
       $('.dtp-btn-ok').click();
   });
   
   $('.calender-open').click(
   function(){
           $('#issue_date').focus();
   });
   
   
   $('[data-toggle="tooltip"]').tooltip(); 
   
   
   $('#product_selector').select2({
     ajax: {
       url: "{{route('relations.product.search')}}",
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
           $(".product-row").each(function() {
               var element = $(this).find('input'); 
                       if ($(element).attr('for') == e.params.data.code) {
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
   
   
       $("#challan_modal").on('hide.bs.modal',function(){
          validate(false);
       });
    function isOdd(num) { return num % 2;}
   function append_row(data){
      var number =($("#products-table .product-row").length + 1);
     var c = 'even';
          if(isOdd(number)){
            var c = 'odd';
         }
    var newRow = $("<tr class='product-row "+c+"'>");
        
          
 var cols = '';
          cols += '<td><a target="_blank" href="{{url('/')}}/products/'+data.position+'">'+data.name+'</a></td>';
          cols += '<td><a target="_blank" href="{{url('/')}}/products/'+data.position+'">'+data.code+'</a></td>';
          cols += '<td>'+data.product_category+'</td>';
          cols += '<td>'+data.product_type+'</td>';
          cols += '<td>'+data.fabric_type+'</td>';
          cols += '<td>'+data.fabric_color+'</td>';
          cols += '<td>'+data.size+'</td>';
   
          cols += '<td style="width:13%"><input reference-number="embroidery_'+number+'" for="'+data.code+'" type="number" min="0" class="form-control" name="embroidery_stock['+data.position+']" value="0" /></td>';
          cols += '<td><button type="button" class="rbtnDel btn btn-sm btn-danger">X</button></td>';
          newRow.append(cols);
          $("table.product-list tbody").append(newRow);
   
         $('.product-row').each(function(index){
            var element =$(this).find('input[reference-number="embroidery_'+number+'"]');
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
               }else{
                  $(this).val(val);
               }
               validate(false);
         {{-- validate_with_stock_quantity(this,false); --}}
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
   
        function validate_with_stock_quantity(element,remove=false){
            $.post("{{route('relations.stock_count.product')}}",{unique:$(element).attr('for'),input:$(element).val(),'on':$(element).attr('reference-number'),'remove':remove},function(response){    
                if(!response.success){
                    $(element).val(Math.abs(response.value));
                    $("#product_message").html(response.message);
                }
            })
        }
   
   $("table#products-table tbody").on("click", ".rbtnDel", function(event) {
   var input = $(this).parent().prev().find('input');
      // validate_with_stock_quantity(input,true);
      $(input).unbind();
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
                          url:'{{route('productions.store')}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                             $.LoadingOverlay("hide");
                            if(response.success){
                               swal({
                                             title: 'Emroidery issued successfully!',
                                             text: 'redirecting to manage embroideries.',
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
               $("#product_message").html('Enter issue embroidery amount.');
            }
         }

         if($(this).val() == 0){
            has_error = true;
            if(show){
               showError($(this).parent().parent(),this);
               $("#product_message").html('Enter issue embroidery amount greater than 0');
            }
         }
      });
      
      if($("input[name='consignor_name']").val() == ''){
           if(!has_error){
              if(show){
                 showError($("input[name='consignor_name']").parent().parent(),$("input[name='consignor_name']"));
                 $("#challan_modal").modal('show');
              }
               has_error= true;
           }
       }

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
      var challan_button_selector = $("#save-challan");

      init_listeners();

      function init_listeners(){
            hideButton();
            hideChallanButton();

             
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
               });

               $(this).change(function() {
                  if($(this).attr('name') == 'consignor_name' || $(this).attr('name') == 'consignor_address' || $(this).attr('name') == 'consignor_gst_no'){
                     challan_validate(false);
                  }else{
                     validate(false);
                  }
               });

               $(this).keyup(function() {
                  if($(this).attr('name') == 'consignor_name' || $(this).attr('name') == 'consignor_address' || $(this).attr('name') == 'consignor_gst_no'){
                     challan_validate(false);
                  }else{
                     validate(false);
                  }
               });
            });
       }

       
    function challan_validate(show = true){
       var has_error = false;
          if($("input[name='consignor_name']").val() == ''){
            if(show){
               showError($("input[name='consignor_name']").parent().parent(),$("input[name='consignor_name']"));
            }
              has_error= true;
          }

         if(!has_error){
            showChallanButton();
         }else{
            hideChallanButton();
         }
          return has_error;
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

      function hideChallanButton(){
         challan_button_selector.prop('disabled',true);
         challan_button_selector.removeClass('btn-info');
         challan_button_selector.addClass('btn-primary');
      }

      function showChallanButton(){
         challan_button_selector.prop('disabled',false);
         challan_button_selector.removeClass('btn-primary');
         challan_button_selector.addClass('btn-info');
      }

@endsection