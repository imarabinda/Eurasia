@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box ">
         <h4 class="page-title font-16"> {{$production->job_work_type}} - {{$production->challan_number}} </h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
         @can('production-edit')
         <a href="{{route('productions.edit',$production->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit Embroidery</a>
         @endcan
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
                           <input type="text" disabled class="form-control" error-text="Enter issue date..." placeholder="yyyy/mm/dd" name="issue_date" id="issue_date" value="{{$production->issue_date}}">
                           <div class="input-group-append bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor Name*</label>
                        <div class="input-group">
                           <input type="text" disabled class="form-control" error-text="Enter vendor name..." placeholder="Enter vendor name" name="vendor_name" value="{{$production->vendor_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor GST No.*</label>
                        <div class="input-group">
                           <input type="text" disabled class="form-control" error-text="Enter vendor gst no..." placeholder="Vendor GST no." name="vendor_gst_no" value="{{$production->vendor_gst_no}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Vendor Address*</label>
                        <div class="input-group">
                           <textarea type="text" disabled class="form-control" error-text="Enter vendor address..." placeholder="Vendor Address" name="vendor_address">{{$production->vendor_address}}</textarea>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="form-group text-left">
                     @can('production-print')
                     <input type="button" target="_blank" href="{{route('productions.print',$production->id)}}" id="print-challan" value="{{trans('Print Challan')}}" class="btn btn-primary">
                     @endcan
      
                     <input type="button" value="{{trans('Consignor Details')}}" id="challan-btn" class="btn btn-primary">

                  </div>
               </div>
            </div></div></div></div>
            <div class="row">


               
      <div class="col-lg-12" style="margin-bottom:70px">
         
      <h4 class="page-title font-16">Products: </h4>
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            <p class="italic" style="color:red" id="product_message"></p>
            <table id="products-table" class="table table-bordered">    
            </table>
         </div>
      </div>
         
                                          


      </div>
   </div>
   </div>
         
   <div class="modal fade bd-example-modal-form" id="challan_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" >
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalform">Consignor Details</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true" class="text-dark">×</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="field-1" class="control-label">Name</label>
                           <input type="text" error-text="Enter consignore name.." disabled class="form-control" name="consignor_name" placeholder="Name" value="{{$production->consignor_name}}">
                        </div>
                        <span class="validation-msg" ></span>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="field-1" class="control-label">Address</label>
                           <div class="input-group"><input error-text="Enter consignore address.."  disabled type="text" class="form-control" name="consignor_address" placeholder="Address" value="{{$production->consignor_address}}">
                           </div>
                           <span class="validation-msg" ></span>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label for="field-1" class="control-label">GST No.</label>
                           <div class="input-group"><input type="text" verify-ignore="true"  disabled class="form-control" name="consignor_gst_no" placeholder="GST No." value="{{$production->consignor_gst_no}}">
                           </div>
                           <span class="validation-msg" ></span>
                        </div>
                     </div>
                  </div>
                  
               </div>
            </div>
         </div>
      </div>
   </div>
</form>


   @can('production-receive')

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
                  <label style="color:black">Receive Embroidery*</label>
                  <input hidden value="0" id="product_id" >
                  <div class="input-group" style="width:100%">
                     <input type="number" for="receive" verify-ignore="true" class="form-control" error-text="Enter Receive embroidery..." placeholder="Add Receive Embroidery" min="1" value="0" name="received_embroidery">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               <div class="form-group mt-3">
                  <label style="color:black">Damage Embroidery</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" for="receive" verify-ignore="true" class="form-control" error-text="Enter damage embroidery..." placeholder="Add Damage Embroidery" min="0" value="0" verify-ignore="true" name="received_damage">
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




var productsTable = $("#products-table").DataTable(  {
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
            url:"{{route('productions.products')}}",
            data:{
                id: {{$production->id}}
            },
            dataType: "json",
            // type:"post"
        },
        "columns": [
           
           
   @can('production-manage-history')
              {
            className: 'details-control-history',
            orderable: false,
            data: null,
            defaultContent: '',
            width: '4%',
            "searchable":false,
        },
   @endcan
            {"title":"Title","data": "product_name","name":"name"},
            {"title":"Item ID","data": "product_code","name":"code"},
            {"title":"Category","data": "product_category.name"},
            {"title":"Fabric Type","data": "fabric_type.name"},
            {"title":"Fabric Color","data": "fabric_color.name"},
            {"title":"Size","data": "size","name":"size"},
            {"title":"Issued Embroidery","data": "issued_quantity"},
            {"title":"Received","data": "log_received_embroideries"},
            {"title":"Damaged","data": "log_received_damage"},
            
   @can('production-receive')
            {"title":"<i class='dripicons-checkmark'></i>","data": "options"},
   @endcan
            ],
              "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],  
               
      createdRow: function (row, data, index) {
        $(row).attr('for-production', data['production_id']);
        $(row).attr('for-product', data['product_id']);
       },   
         
         }); 
@can('production-print')
     $("#print-challan").click(function(e){
           e.preventDefault();
           window.open($(this).attr('href'));
       }) 
@endcan

       $('#challan-btn').on('click',function(e){
        e.preventDefault();
        $('#challan_modal').modal('show');
       });
       
       
       
       //receive
       
       var stitching_id, product_id = null;
@can('production-receive')
   
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
          var re = $('input[name="received_embroidery"]').val();
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
   
         $.post('{{route("productions.receive",$production->id)}}',{received_embroidery:parseInt(re,10),received_damage:parseInt(re_dam,10),product:product_code},function(response){
            $.LoadingOverlay("hide"); 
            if(response.success){
               $("#receive-modal").modal('hide');
               swal({
                                             title: 'Issued embroidery stock received successfully!',
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



  var receive_button_selector = $(".update-receive");

      init_listeners();
      function init_listeners(){

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

               
            });
       }


       $("#receive-modal").on('hide.bs.modal',function(){
          $("#product_id").val(0);
       });
       
       function validate_receive_input(show = true){
            var has_error =true;
         var re = $('input[name="received_embroidery"]').val();
         var re_dam = $('input[name="received_damage"]').val();
      
         if(re == ''){
            if(show){
               showError($('input[name="received_embroidery"]').parent().parent(),$('input[name="received_embroidery"]'));   
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










       
   @can('production-manage-history')
    $('#products-table tbody').on('click', 'td.details-control-history', function () {
    var tr = $(this).closest('tr');
    
    product_id = $(tr).attr('for-product');
    production_id = $(tr).attr('for-production');
        

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
            url:"{{route('productions.receive.history')}}",
            data:{
                production_id:production_id,
                product_id:product_id,
            },
            dataType: "json",
            // type:"post"
        },
        createdRow: function (row, data, index) {
        $(row).addClass("highlightExpandedSecond");
    },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
        "columns": [
           {"title":"Date","data": "created_at"},
            {"title":"Received Embroidery","data": "received_embroidery"},
            {"title":"Received Damage","data": "received_damage",},
            ],

            });
    }
   @endcan
@endsection