@extends('template.index')
@section('content')
 



    <div class="row">
       <div class="col-md-12">
        <div class="page-title-box">
            @can('stitching-create')
           <a href="{{route('stitches.create')}}" class="btn btn-info btn-action"><i class="dripicons-plus"></i> {{__('Issue new stitching')}}</a>
        @endcan
        </div>
        </div>
    </div>

                            <div class="row">
                                <div class="col-12">
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="stitching-data-table" class="table table-bordered" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                                @can('stitching-view')
                                                            <th class="not-exported"></th>
                                                            @endcan
                                                            <th>{{trans('Issue Date')}}</th>
                                                            <th>{{trans('Vendor Name')}}</th>
                                                            <th>{{trans('Jobwork Type')}}</th>
                                                            <th>{{trans('Total Products')}}</th>
                                                            <th>{{trans('Received Products')}}</th>
                                                            <th class="not-exported">{{trans('Action')}}</th>
                                                        </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 


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
                  <label style="color:black">Receive Stitching*</label>
                  <input hidden value="0" id="product_id" >
                  <input hidden value="0" id="stitching_id" >
                  <div class="input-group" style="width:100%">
                     <input type="number" verify-ignore="true" class="form-control" error-text="Enter Receive stitching..." placeholder="Add Receive Stitching" min="1" value="0" name="received_stitches">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               <div class="form-group mt-3">
                  <label style="color:black">Damage Stitching</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" verify-ignore="true" class="form-control" error-text="Enter damage stitching..." placeholder="Add Damage Stitching" min="0" value="0" verify-ignore="true" name="received_damage">
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

    


@can('stitching-manage')
        var productsTable = $('#stitching-data-table').DataTable( {
            responsive: false,
            scrollX:true,
            fixedHeader: {
                header: true,
                footer: true
            },
            "processing": true,
            "serverSide": true,
            "serverMethod": 'post',
            "ajax":{
                url:"<?php echo route('stitches.list'); ?>",
                data:{
                    // all_permission: all_permission
                },
                dataType: "json",
                // type:"post"
            },
            "columns": [
                @php
                $col = 5;
            @endphp
        @can('stitching-view')
        @php
                $col = $col +1;
            @endphp
                {
            className: 'details-control',
            orderable: false,
            data: null,
            defaultContent: '',
            width: '4%',
            "searchable":false,
        },
        @endcan
        {"data": "issue_date"},
                {"data": "vendor_name",name:'tailor.name'},
                {"data": "job_work_type"},
                {"data": "products_count","orderable":false},
                {"data": "received_products","searchable":false,"orderable":false},
                {"data": "options"},
            ],
            order:[[{{$col}},'desc']],
            'language': {
                'searchPlaceholder': "{{trans('Type Vendor Name , Vendor address , Jobwork type or Issue date...')}}",
                'lengthMenu': '_MENU_ {{trans("records per page")}}',
                 "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                "search":  '{{trans("Search")}}',
                'paginate': {
                        'previous': '<i class="dripicons-chevron-left"></i>',
                        'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            select: {
        style:    'os',
        selector: 'td:not(:first-child)'
    },

    
            "createdRow": function( row, data, dataIndex ) {
                $(row).attr('for', data['id']);
                },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],

            dom:'<"row"lfB>rtip',
            'select': { style: 'os', selector: 'td:not(:first-child)'},
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        
            buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                        download('{{route('export.stitches')}}');   
                                            
                    }
                }
                ,
                
                {
                    extend: 'colvis',
                    text: '{{trans("Column visibility")}}',
                    {{-- columns: ':gt()' --}}
                },
                ],
        
        
        } );
@endcan
        var id, stitching_id,product_id = null;
    @can('stitching-view') 
        $('#stitching-data-table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    
    id = $(tr).attr('for');
        

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

    var childTable = {};

    function createChild ( row ,tr, sub = true) {
    // This is the table we'll convert into a DataTable
    var table = $('<table class="display" width="100%"/>');
    // Display it the child row
    
    row.child( table ).show();
    $(tr).next().addClass('no-padding');
    
     if(sub){
        sub_table(table);
    }else{
        history_table(table);    
    }
           
}
        
        function destroyChild(row) {
            var table = $("table", row.child());
            table.detach();
            table.DataTable().destroy();
            
            // And then hide the row
            row.child.hide();
        }
        
        
        function sub_table(table){
            
    // Initialise as a DataTable
    childTable[id] = table.DataTable(  {
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
            url:"{{route('stitches.products')}}",
            data:{
                id: id
            },
            dataType: "json",
            // type:"post"
        },
          createdRow: function (row, data, index) {
        $(row).addClass("highlightExpanded");
        $(row).attr('for-stitching', data['stitching_id']);
        $(row).attr('for-product', data['product_id']);
    },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
        "columns": [
            @php
                $col = 9;
            @endphp
            @can('stitching-manage-history')
            @php
                $col += 1;
            @endphp
                    {
            className: 'details-control-history',
            orderable: false,
            data: null,
            defaultContent: '',
            width: '4%',
            "searchable":false,
        },
        @endcan
            {"title":"Title","data": "product_name","name":"product.name"},
            {"title":"Item ID","data": "product_code","name":"product.code"},
            {"title":"Category","data": "product.product_category.name"},
            {"title":"Fabric Type","data": "product.fabric_type.name"},
            {"title":"Fabric Color","data": "product.fabric_color.name"},
            {"title":"Size","data": "size","name":"size"},
            {"title":"Embroidery Stock Quantity","data": "received_embroidery"},
            {"title":"Issued Stitching","data": "issued_quantity"},
            {"title":"Received","data": "log_received_stitches"},
            {"title":"Damaged","data": "log_received_damage"},
            @can('stitching-receive')
            
            @php
                $col += 1;
            @endphp
            
            {"title":"<i class='dripicons-checkmark'></i>","data": "options"},
            @endcan
            ],
            
            order:[[{{$col,'desc'}}]],

            });   
            
            child_add_listner($(table[0]).attr("id"));
        }
        
    @endcan
    @can('stitching-receive')
   $(document).on("click", ".receive-button", function(){
        $("#receive_message").html('');
        hideReceiveButton();
          $("#receive-form").trigger('reset');
           $("#product_id").val($(this).data('code'));
           $("#stitching_id").val($(this).data('id'));
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
            
            var new_id = $("#stitching_id").val();
            $.post('{{url('/stitches')}}/'+new_id+'/receive',{received_stitches:re,received_damage:re_dam,product:$('#product_id').val()},function(response){
                $.LoadingOverlay("hide"); 
                if(response.success){
                    id = new_id;
                    $("#receive-modal").modal('hide');
                    swal({
                        title: 'Issued stitching stock received successfully!',
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
                                childTable[new_id].draw();
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
           $("input").each(function(){

               if($(this).attr('type') == 'number'){
                  $(this).change(function(){
                     if($(this).val() == ''){
                        $(this).val(0);
                     }else{
                        $(this).val(parseInt($(this).val(),10));
                     }

                     validate_receive_input(false);
                      hideError($(this).parent(),this);
                  });
                  $(this).keyup(function(){
                     if($(this).val() == ''){
                        $(this).val(0);
                     }else{
                        $(this).val(parseInt($(this).val(),10));
                     }
                     validate_receive_input(false);
                     hideError($(this).parent(),this);
                  });
                  return;
               }
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
    },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
        "columns": [
            {"title":"Date","data": "created_at"},
            {"title":"Received Stitches","data": "received_stitches"},
            {"title":"Received Damage","data": "received_damage",},
            ],
            order:[[0,'desc']],

            });
    }
      
//strt

function child_add_listner(table){

    $('#'+table+' tbody').on('click', 'td.details-control-history', function () {
    var tr = $(this).closest('tr');
    
    product_id = $(tr).attr('for-product');
    stitching_id = $(tr).attr('for-stitching');
        

    var row = childTable[stitching_id].row( tr );
    if ( row.child.isShown() ) {
        // This row is already open - close it
        destroyChild(row);
        tr.removeClass('shown');
        {{-- tr.removeClass('highlightExpanded'); --}}
    }
    else {
        // Open this row
        createChild(row,tr,false);
        tr.addClass('shown');
        {{-- tr.addClass('highlightExpanded'); --}}
    }
        } );

        }
           //edn
           
@endcan
@endsection