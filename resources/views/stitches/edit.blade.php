@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$stitching->job_work_type}} - {{$stitching->challan_number}} </h4>
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
                           <input type="text" class="form-control" error-text="Enter issue date..." placeholder="yyyy/mm/dd" name="issue_date" id="issue_date" value="{{$stitching->issue_date}}">
                           <div class="input-group-append bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                   <div class="col-md-5">
                     <div class="form-group">
                        <label>Select Vendor*</label>

                        <select verify-ignore="false" error-text="@if(count($tailors) > 0) Select Vendor @else Add Tailor First @endif" class="select2 form-control mb-3 custom-select js-states form-control" id="tailor" name="tailor" style="width: 100%; height:36px;">
                           <option></option>
                           @foreach($tailors as $tailor)
                           <option @if($tailor->id == $stitching->tailor_id) selected @endif value="{{$tailor->id}}" data-json="{{json_encode($tailor)}}">{{$tailor->name}}</option>
                           @endforeach
                        </select>
                        
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
   
                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Vendor Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter vendor name..." placeholder="Enter vendor name" name="vendor_name" value="{{$stitching->vendor_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor GST No.</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter vendor gst no..." placeholder="Vendor GST no." verify-ignore="true" name="vendor_gst_no" value="{{$stitching->vendor_gst_no}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor Address</label>
                        <div class="input-group">
                           <textarea type="text" verify-ignore="true" class="form-control" error-text="Enter vendor address..." placeholder="Vendor Address" name="vendor_address">{{$stitching->vendor_address}}</textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>


               <div class="col-md-12">
                  <div class="form-group text-left">
                     @can('stitching-print')
                     <input type="button" target="_blank" href="{{route('stitches.print',$stitching->id)}}" id="print-challan" value="{{trans('Print Challan')}}" class="btn btn-primary">
                     @endcan
                     <input type="button" value="{{trans('Edit Consignor Details')}}" id="challan-btn" class="btn btn-primary">
                  </div>
               </div>

               
            </div>
         </div>
      </div>
   </div>   


   
                           @php
      
      $products_pre = \App\Models\Stitching::find($stitching->id)->embroidery_stocks()
      ->with(['product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width']);
      $products_pre->select(['workables.issued_quantity','embroidery_stocks.*','workables.workable_id as stitching_id'])->leftJoin('final_stock_logs as logs',function($join){
            $join->on('logs.product_id','=','workables.product_id')->whereColumn('logs.stitching_id','=','workables.workable_id');
      })->groupBy('workables.product_id','logs.stitching_id')->selectRaw('IFNULL(sum(logs.received_damage),0) as log_received_damage')->selectRaw('IFNULL(sum(logs.received_stitches),0) as log_received_stitches');
      
      $products_pre = $products_pre->get();
      
      $pre_ids = $products_pre->pluck('id')->toArray();
      $products_pre = $products_pre->toArray();
      
      
      $products_new =array();
      if(isset($items)){
         $id = $stitching->id;
         $item_ids = array_diff(array_keys($items),$pre_ids);
         $products_new = \App\Models\EmbroideryStock::whereIn('id',$item_ids)->with(['product','product.fabric_type:id,name','product.product_type:id,name','product.fabric_color:id,name','product.product_category:id,name','product.size:id,height,width'])
         ->get()->toArray();
      }
      $embroidery_stocks = array_merge($products_pre,$products_new);
         
         @endphp



   <div class="row">
      <div class="col-md-3" >
         <h4 class="page-title font-16">Products: </h4>


         <div class="form-group text-left">
                     <input type="button" value="{{trans('Add Products')}}" id="add-btn" class="btn btn-primary">
                  </div>
                
      </div>
      
      <div class="col-lg-12" style="margin-bottom:70px">
         
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
                              <th class="dt-center">{{trans('Issue Stitches Quantity')}}</th>
                              <th class="dt-center">{{trans('Received Stitches')}}</th>
                              <th class="dt-center">{{trans('Damage Stitches')}}</th>
                              @can('stitching-receive')
                              <th class="dt-center"><i class="dripicons-checkmark"></i></th>
                              @endcan
                              @can('stitching-remove-product')
                              <th class="dt-center"><i class="dripicons-trash"></i></th>
                              @endcan
                           </tr>
                        </thead>
                        <tbody>
                           
                           
   @if($embroidery_stocks)

   @foreach($embroidery_stocks as $embroidery_stock)
   
         @php
         
         $product = $embroidery_stock['product'];

         if($loop->index % 2){
            $class = 'even';
         }else{
            $class = 'odd';
         }
         @endphp
         <tr class="{{$class}} product-row">
          <td><a target="_blank" href="{{route('products.show',$product['id'])}}">{{$product['name']}}</a></td>
          <td><a target="_blank" href="{{route('products.show',$product['id'])}}">{{$product['code']}}</a></td>
          <td>{{$product['product_category']['name']}}</td>
          <td>{{$product['product_type']['name']}}</td>
          <td>{{$product['fabric_type']['name']}}</td>
          <td>{{$product['fabric_color']['name']}}</td>
          <td>{{$product['size']['height']}} x {{$product['size']['width']}}</td>
   
          @php
           
           if(array_key_exists('pivot',$embroidery_stock)){

              $issue = $value = $embroidery_stock['pivot']['issued_quantity'];
              
              if(!empty($items) && array_key_exists($embroidery_stock['id'],$items)){
                 $issue = $value+$items[$embroidery_stock['id']];
              }
           }else{
               $value = 0;
              $issue = $items[$embroidery_stock['id']];
           }
           $t = 0;
           if(array_key_exists('log_received_stitches',$embroidery_stock) && array_key_exists('log_received_damage',$embroidery_stock)){
              $t = $embroidery_stock['log_received_stitches'] + $embroidery_stock['log_received_damage'];
           }
          @endphp
          <td style="width:13%"><input @cannot('stitching-remove-product') disabled @endcan @if($t > 0) disabled @endif reference-number="stitches_{{$loop->index}}" for="{{$embroidery_stock['id']}}" type="number" min="0" class="form-control" name="stitching_stock[{{$embroidery_stock['id']}}]" value="{{$issue}}" /></td>
          
          <td> <input class="form-control" disabled to="receive[{{$product['code']}}]" value="{{array_key_exists('log_received_stitches',$embroidery_stock) ? $embroidery_stock['log_received_stitches'] : 0}}" /></td>
          <td> <input class="form-control" disabled to="damage[{{$product['code']}}]" value="{{array_key_exists('log_received_damage',$embroidery_stock) ? $embroidery_stock['log_received_damage'] : 0}}" /></td>
          @can('stitching-receive')
          @if($t < $value)
          <td>
            <input type="button" value="Receive" data-product="{{$product['name']}}" data-code="{{$product['code']}}" data-id="{{$stitching->id}}" class="btn btn-danger receive-button">
          </td>
            @elseif($value==0)
               <td>
               </td>      
            @else
            <td>
            <input type="button" value="Totally Received"  class="btn btn-success">
            </td>
            @endif
            @endcan
            
            @can('sttiching-remove-product')
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
      
      </div>
               <div class="col-md-12" id="button">
                  <div class="form-group text-center">
                     <input type="button" value="{{trans('Update Stitches')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>
   </div>
   
   <div class="modal fade bd-example-modal-form" id="challan_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
      <div class="modal-dialog modal-lg" >
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalform">Edit Consignor Details</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true" class="text-dark">×</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     
                     @php
                       $selects = \Illuminate\Support\Facades\DB::table('stitches')->groupBy('consignor_name','consignor_address','consignor_gst_no')->select(['consignor_name','consignor_address','consignor_gst_no'])->get();  
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
                           <input type="text" verify-ignore="true" error-text="Enter consignor name.." class="form-control" name="consignor_name" placeholder="Name" value="{{$stitching->consignor_name}}">
                        </div>
                        <span class="validation-msg" ></span>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="field-1" class="control-label">Address</label>
                           <div class="input-group"><input verify-ignore="true" error-text="Enter consignor address.." type="text" class="form-control" name="consignor_address" placeholder="Address" value="{{$stitching->consignor_address}}">
                           </div>
                           <span class="validation-msg" ></span>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="field-1" class="control-label">GST No.</label>
                           <div class="input-group"><input type="text" verify-ignore="true" class="form-control" name="consignor_gst_no" placeholder="GST No." value="{{$stitching->consignor_gst_no}}">
                           </div>
                           <span class="validation-msg" ></span>
                        </div>
                     </div>
                  </div>
                  
               </div>
            </div>
            @can('sttiching-save-challan')
            <div class="modal-footer">                                                             
               <button type="button" class="btn btn-primary" id="save-challan">Save</button>
            </div>
            @endcan
         </div>
      </div>
   </div>

</form>
@can('stitching-receive')
<div id="receive-modal" tabindex="-1" role="dialog" aria-labelledby="roll" aria-hidden="true" class="modal fade text-left">
   <div role="document" class="modal-dialog" style="max-width:250px!important">
      <div class="modal-content">
         <div class="modal-header">
            <h6 class="modal-title" id="receive_name"></h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="receive-form">
               <div class="form-group mt-3">
                  <label style="color:black">Receive Stitches*</label>
                  <input hidden value="0" id="product_id" >
                  <div class="input-group" style="width:100%">
                     <input type="number" for="receive" verify-ignore="true" class="form-control" error-text="Enter Receive stitches..." placeholder="Add Receive Stitches" min="1" value="0" name="received_stitches">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               <div class="form-group mt-3">
                  <label style="color:black">Damage Stitches</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" for="receive" verify-ignore="true" class="form-control" error-text="Enter damage stitches..." placeholder="Add Damage Stitches" min="0" value="0" verify-ignore="true" name="received_damage">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               <p class="italic" style="color:red" id="receive_message"></p>
               <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary update-receive">{{trans('Add')}}</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endcan
@endsection
@section('script')

@can('stitching-add-product')
   var add_clicked = false;
   $('#add-btn').on('click',function(e){
      e.preventDefault();
      add_clicked = true;
      $('#submit-btn').click();
   });
@endcan
@can('stitching-remove-product')
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
   
   $('[data-toggle="tooltip"]').tooltip(); 
   
@can('stitching-add-product')
   
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

   
   function formatRepoSelection (repo) {
   return repo.name || repo.text;
   }

@endcan

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


   
   $("#tailor").select2({
                        width: '100%',
                        minimumResultsForSearch: Infinity,
                        placeholder: $("#tailor").attr('error-text'), 
   });
                     
   $('#tailor').on('select2:select',function(e){
      
      var json = JSON.parse(e.params.data.element.dataset.json);
      $("input[name='vendor_name']").val(json.name);
      $("textarea[name='vendor_address']").val(json.address);
      $("input[name='vendor_gst_no']").val(json.gst_no);
      validate(false);
   });

   var stitching_id, product_id = null;
   @can('stitching-receive')
   $(document).on("click", ".receive-button", function(){
         $("#receive_message").html('');
         hideReceiveButton();
          $("#receive-form").trigger('reset');
           $("#product_id").val($(this).data('code'));
           $('#receive_name').html('Adding >> '+$(this).data('product') +' || '+ $(this).data('code'));
           $('#receive-modal').modal('show');
      });
   
   
   
     $('.update-receive').click(function(e){
         e.preventDefault();
          var re = $('input[name="received_stitches"]').val();
          var re_dam = $('input[name="received_damage"]').val();
         
        
         if(!validate_receive_input()){
             return;
         }
         $.LoadingOverlay("show");
         var formData = new FormData();
                        // Append all form inputs to the formData Dropzone will POST
                        var data = $("#receive-form").serializeArray();
                        $.each(data, function (key, el) {
                            formData.append(el.name, el.value);
                        });

         var product_code = $('#product_id').val();
   
         $.post('{{route("stitches.receive",$stitching->id)}}',{received_stitches:parseInt(re,10),received_damage:parseInt(re_dam,10),product:product_code},function(response){
            $.LoadingOverlay("hide"); 
            if(response.success){
               $("#receive-modal").modal('hide');
               swal({
                                             title: 'Issued stitches stock received successfully!',
                                             text: '',
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
                                                   if(re > 0){
                                                      $('input[to="receive['+product_code+']"]').val(response.receive);
                                                   }
                                                   if(re_dam > 0){
                                                      $('input[to="damage['+product_code+']"]').val(response.damage);
                                                   }
                                                   
                                                   productsTable.draw();
                                                }
                                    });
             }else{
                 $("#receive_message").html(response.message);
             }
         })
     });
   @endcan
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
     
   
     $("#print-challan").click(function(e){
         e.preventDefault();
         window.open($(this).attr('href'));
     })
@can('stitching-save-cahllan')
    $('#save-challan').click(function(e){
        e.preventDefault();
          
          var data = {
              consignor_name:$("input[name='consignor_name']").val(),
              consignor_address:$("input[name='consignor_address']").val(),
              consignor_gst_no:$("input[name='consignor_gst_no']").val()
          }
          if(!challan_validate()){
               $.LoadingOverlay("show");
              $.post('{{route("stitches.save_challan",$stitching->id)}}',data,function(response){
               $.LoadingOverlay("hide");   
               if(response.success){
                      $("#challan_modal").modal('hide');
                     swal({
                                             title: 'Stitches Consignor Details updated successfully!',
                                             text: '',
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
                                                   
                                                }
                                    })
                     }
              })
          }
    });
     @endcan
   
   
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
                            url:'{{route('stitches.update',$stitching->id)}}',
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
      
      {{-- if($("input[name='consignor_name']").val() == ''){
           if(!has_error){
              if(show){
                 showError($("input[name='consignor_name']").parent().parent(),$("input[name='consignor_name']"));
                 $("#challan_modal").modal('show');
              }
               has_error= true;
           }
       } --}}
      

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
      var receive_button_selector = $(".update-receive");

      init_listeners();
      function init_listeners(){
            hideButton();
            hideChallanButton();
            hideReceiveButton();

            
            $("textarea").change(function(){
               validate(false);
            });

            $("textarea").keyup(function(){
               validate(false);
            });
            


           $("input").each(function(){


               if($(this).attr('type') == 'number'){
                  $(this).change(function(){
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

               if($(this).attr('for') == 'receive'){
                  validate_receive_input(false);
               }else{
                  validate(false);
               }

                  });

                  $(this).keyup(function(){
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
               if($(this).attr('for') == 'receive'){
                  validate_receive_input(false);
               }else{
                  validate(false);
               }
                  });
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

                  hideError($(this).parent(),this);
               });

               $(this).keyup(function() {
                  if($(this).attr('name') == 'consignor_name' || $(this).attr('name') == 'consignor_address' || $(this).attr('name') == 'consignor_gst_no'){
                     challan_validate(false);
                  }else{
                     validate(false);
                  }
                  hideError($(this).parent(),this);
               });

            });
       }


       $("#receive-modal").on('hide.bs.modal',function(){
          $("#product_id").val(0);
       });
       
       function validate_receive_input(show = true){
            var has_error =true;
         var re = $('input[name="received_stitches"]').val();
         var re_dam = $('input[name="received_damage"]').val();
      
         if(re == ''){
            if(show){
               showError($('input[name="received_stitches"]').parent().parent(),$('input[name="received_stitches"]'));   
            }
          has_error = false;
         }
         
         if($('#product_id').val() == '' || $('#product_id').val() == 0){
            $("#receive_message").html("Do not open directly, try to avoid your tricks. Use this skills and implement something new.");
            has_error = false;
         }
         
         if(re_dam == ''){
            if(show){
               showError($('input[name="received_damage"]').parent().parent(),$('input[name="received_damage"]'));   
            }
          has_error =  false;
         }

         if(isNaN(re) || isNaN(re_dam)){
            has_error = false;
            $("#receive_message").html("Provide numbers not strings.");
         }

         if(has_error){
            showReceiveButton();
         }else{
            hideReceiveButton();
         }
         return has_error;
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

    
       {{-- $("#challan_modal").on('hide.bs.modal',function(){
          validate(false);
       }); --}}

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
      
      function hideReceiveButton(){
         receive_button_selector.prop('disabled',true);
         receive_button_selector.removeClass('btn-info');
         receive_button_selector.addClass('btn-primary');
      }

      function showReceiveButton(){
         receive_button_selector.prop('disabled',false);
         receive_button_selector.removeClass('btn-primary');
         receive_button_selector.addClass('btn-info');
      }



      
       
@can('stitching-manage-history')
    $('#products-table tbody').on('click', 'td.details-control-history', function () {
    var tr = $(this).closest('tr');
    
    product_id = $(tr).attr('for-product');
    stitching_id = $(tr).attr('for-stitching');
        

    var row = productsTable.row( tr );
    if ( row.child.isShown() ) {
        // This row is already open - close it
        destroyChild(row);
        tr.removeClass('shown');
        {{-- tr.removeClass('highlightExpanded'); --}}
    }
    else {
        // Open this row
        createChild(row,tr);
        tr.addClass('shown');
        {{-- tr.addClass('highlightExpanded'); --}}
    }
        } );




    function createChild ( row ,tr, sub = true) {
    // This is the table we'll convert into a DataTable
    var table = $('<table class="display" width="100%"/>');
    // Display it the child row
    
    row.child( table ).show();
    $(tr).next().addClass('no-padding');
     if(sub){
        history_table(table);
    }else{
        sub_table(table);    
    }
           
   }
   
        
        function destroyChild(row) {
            var table = $("table", row.child());
            table.detach();
            table.DataTable().destroy();
            
            // And then hide the row
            row.child.hide();
        }
        

        
function history_table(table){            
    // Initialise as a DataTable
    var historyTable = table.DataTable(  {
        responsive: false,
        scrollX:false,
        "paging":   false,
        "searching": false,
        "info":     false,
        fixedHeader: {
            header: true,
            footer: true
        },
        "processing": true,
        "serverSide": true,
        "serverMethod": 'post',
        "ajax":{
            url:"{{route('stitches.receive.history')}}",
            data:{
                stitching_id:stitching_id,
                product_id:product_id,
            },
            dataType: "json",
            // type:"post"
        },
        createdRow: function (row, data, index) {
        $(row).addClass("highlightExpandedSecond");
    },
        "columns": [
           {"title":"Date","data": "created_at"},
            {"title":"Received Stitches","data": "received_stitches"},
            {"title":"Received Damage","data": "received_damage",},
            ],"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],

            });
    }
    @endcan
@endsection