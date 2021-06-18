@extends('template.index')
@section('content')
 



    <div class="row">
       <div class="col-md-12">
        <div class="page-title-box">
            @can('production-create')
            
           <a href="{{route('productions.create')}}" class="btn btn-info btn-action"><i class="dripicons-plus"></i> {{__('Issue new embroidery')}}</a>
        @endcan
        </div>
        </div>
    </div>

                            <div class="row">
                                <div class="col-12">
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="stock-data-table" class="table table-bordered" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                            @can(['stitching-add-product','stitching-create'])
                                                            <th></th>
                                                            @endcan
                                                            <th>{{trans('Receive Date')}}</th>
                                                            <th>{{trans('Product Id')}}</th>
                                                            <th>{{trans('Product Name')}}</th>
                                                            <th>{{trans('Fabric Type')}}</th>
                                                            <th>{{trans('Fabric Color')}}</th>
                                                            <th>{{trans('Stock Quantity')}}</th>
                                                            <th>{{trans('Damage Quantity')}}</th>
                                                        </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 

@endsection


@section('script')

    




        var selected_data = {};

        var table = $('#stock-data-table').DataTable( {
            responsive: false,
            scrollX:true,
            fixedHeader: {
                header: true,
                footer: true
            },
            "select":true,
            "processing": true,
            "serverSide": true,
            "serverMethod": 'post',
            "ajax":{
                url:"<?php echo route('productions.stock.list'); ?>",
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
            
            @can(['stitching-add-product','stitching-create'])
                
            {"data":"key"},
            @php
                $col = $col + 1;
            @endphp
            
            @endcan
                {"data": "updated_at"}, 
                {"data": "product_code",name:"product.code"},
                {"data": "product_name",name:"product.name"},
                {"data": "product.fabric_type.name"},
                {"data": "product.fabric_color.name"},
                {"data": "remaining"},
                {"data": "received_damage"},
            ],
            "order":[[{{$col}},'desc']],
            'language': {
                'searchPlaceholder': "{{trans('Type Issue date or Product name...')}}",
                'lengthMenu': '_MENU_ {{trans("records per page")}}',
                 "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                "search":  '{{trans("Search")}}',
                'paginate': {
                        'previous': '<i class="dripicons-chevron-left"></i>',
                        'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            "createdRow": function( row, data, dataIndex ) {
                $(row).attr('data-key', data['key']);
                $(row).attr('data-max', data['remaining']);
                },

            'columnDefs': [
            
                {"className": "dt-center", "targets": "_all"},
               @can(['stitching-add-product','stitching-create'])
             
                {
                    'render': function(data, type, row, meta){

                        if(type === 'display'){
                            var checked = '';
                            if(data in selected_data){
                                checked = 'checked="checked"'
                            }
                            data = '<div  class="checkbox"><input '+checked+' type="checkbox"  class="dt-checkboxes"><label></label></div>';
                        }
                       return data;
                    },
                    'targets': [0]
                }
                @endcan
            ], 
            
            dom:'<"row"lfB><"">rt<"bottombuttons">ip',
            @can(['stitching-add-product','stitching-create'])
            'select': { style: 'multi', selector: 'td:first-child'},
            @endcan
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
             buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                         download('{{route('export.embroidery_stocks')}}',{
                            ids:selected_data
                        });   
                                            
                    }
                }
                    
                ,
                
                {
                    extend: 'colvis',
                    text: '{{trans("Column visibility")}}',
                    {{-- columns: ':gt()' --}}
                },
                ],
            
         initComplete:function(){
            @can(['stitching-add-product','stitching-create'])
            $(".bottombuttons").html('<div class="col-md-4 mt-3"><div class="form-group"><button type="button" disabled class="btn btn-secondary waves-effect btn-primary" id="submit-btn"><?php if(isset($id)){
                if(Auth::user()->can('stitching-add-product')){
                    echo 'Add Selected';
                }
         }else{
             if(Auth::user()->can('stitching-create')){    
             echo 'Issue Stitching';
            }
         }?></button></div></div>');
         @endcan
         submit_button_selector = $("#submit-btn");
            init_listeners(); 
        }
        } );




var submit_button_selector = null;
            

function format ( key,d ) {
    var input_name = 'issue_stitching['+key+']';
    var val = 0;
    if(key in selected_data){
        val = selected_data[key];
    }
    return '<table >'+
        '<tr>'+
            '<td>Issue Stitching Quantity:</td>'+
            '<td width="50%">'+
                '<div class="input-group">'+
                           '<input for="'+key+'" name="'+input_name+'" class="form-control" error-text="Enter issue quantity..." type="number" min="0" max="'+d.remaining+'" value="'+val+'"/></td>'+
                         '</div>'+
                        
        '</tr>'+
    '</table>';
}
       

    
    $('#stock-data-table').on('draw.dt',function(){
        $( "input:checked" ).change();

    });

    $('#stock-data-table tbody').on('change','input[type="checkbox"]',function(){
        
        var tr = $(this).closest('tr');
        
        var key = $(tr).attr('data-key');
        if(key == 0 ){
            alert('0 invented by aryabhatt! naahh i think its u.')
            return;
        }

        var max = $(tr).attr('data-max');

        var row = table.row(tr);

        var input_name = 'input[name="issue_stitching['+key+']"';
        
        if ( row.child.isShown() ) {
            row.child.remove();
            tr.removeClass('shown');
            $(input_name).unbind();
            delete selected_data[key];
        }
        else {

            if(!(key in selected_data)){
                selected_data[key] = 0;
            }
            
            row.child( format(key,row.data()) ).show();
            individual_input_listner(input_name);
            tr.addClass('shown');
            $(tr).next().addClass('no-padding highlightExpanded');
        }

        validate(false);
    });

    function validate(show = false){
        var has_error = false;

        $("input[type='number']").each(function(){
            if($(this).attr('verify-ignore') != 'true'){

                
                if($(this).val() <= 0){
                    has_error = true;
                }
                
                if($(this).val() > parseInt($(this).attr('max'),10) || $(this).val() < parseInt($(this).attr('min'),10) ){
                    has_error = true;
                }

            }
        });

        if(jQuery.isEmptyObject(selected_data)){
            has_error = true;
        }


        var array = Object.values(selected_data);
        var zero = array.includes(0);

        if(zero){
            has_error = true;
        }


        if(has_error){
            hideButton();
        }else{
            showButton();
        }


        return has_error;
    }
    



      function init_listeners(){
            hideButton();

            $("#submit-btn").click(function(e){
              e.preventDefault();
              $.LoadingOverlay("show"); 
              $.post('{{route('stitches.bucket')}}',{items:selected_data<?php
              if(isset($id)){
                  echo ',id:'.$id;
              }
              ?>},function(response){
                $.LoadingOverlay("hide");     
                console.log(response);
                if('success' in response && response.success){
                       if('redirect' in response){
                           location.href = response.redirect;
                       }
                    }else{

                    }
              });

          });
       }
    

        

function individual_input_listner(input){
    
    $(input).keyup(function(){
        
        
        var val = parseInt($(this).val(),10);
        var max = parseInt($(this).attr('max'));
        var min = parseInt($(this).attr('min'));
        var r = 0;
        if(val > max){
            $(this).val(max);
            r = max;
        }else if(val < min){
            $(this).val(min);
            r = min;
        }else if(isNaN(val)){
            $(this).val(r);
            r = r;
        }
        else{
            $(this).val(val);
            r = val;
        }

        selected_data[$(this).attr('for')] = r;
        validate(false); 
    });
    
    $(input).change(function(){
        var val = parseInt($(this).val(),10);
        var max = parseInt($(this).attr('max'));
        var min = parseInt($(this).attr('min'));
        var r = 0;
        if(val > max){
            $(this).val(max);
            r = max;
        }else if(val < min){
            $(this).val(min);
            r = min;
        }else if(isNaN(val)){
            $(this).val(r);
            r = r;
        }else{
            $(this).val(val);
            r = val;
        }

        selected_data[$(this).attr('for')] = r;
        
        validate(false);  
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