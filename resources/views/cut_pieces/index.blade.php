@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Available Cut Pieces</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>Filter cut pieces using fabric type , fabric color & size </small></p>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-12 mt-2">
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            
            <div class="row">
                <div style="width:10%;padding-left:15px">
                     <div class="form-group">
                        <select error-text="Select fabric type" relation="fabric_colors" required name="fabric_type" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option value="0">All</option>
                                                        
                           @foreach($fabric_types as $fabric_type)
                           <option value="{{$fabric_type->id}}" is-relation="@php if(count($fabric_type->colors) > 0) echo true; @endphp">{{$fabric_type->name}}</option>
                           @endforeach
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


                  <div style="width:13%;padding-left:15px">
                       <div class="form-group">
                        <select error-text="Select fabric color" required name="fabric_color" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                                                        <option value="0">All</option>
                                                        @foreach($fabric_colors as $fabric_color)
                                                        <option value="{{$fabric_color->id}}">{{$fabric_color->name}}</option>
                                                     @endforeach
                                                     
                                                    </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


                   <div style="width:10%;padding-left:15px">
                      <div class="form-group">
                        <select error-text="Select size" required name="size" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                                                        <option value="0">All</option>
                                                        @foreach($sizes as $size)
                                                        <option value="{{$size->id}}">{{$size->height}} x {{$size->width}}</option>
                                                     @endforeach
                                                    </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


            </div>
    

            <table id="cut-pieces-data-table" class="table table-bordered" style="width: 100%">
               <thead>
                  <tr>
                     <th>{{trans('Fabric Type')}}</th>
                     <th>{{trans('Fabric Color')}}</th>
                     <th>{{trans('Size')}}</th>
                     <th>{{trans('Total Pieces')}}</th>
                     <th>{{trans('Used Pieces')}}</th>
                     <th>{{trans('Remaining Pieces')}}</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <!-- end col -->
</div>
<!-- end row --> 
        
@endsection

@section('script')
   


               var table = $('#cut-pieces-data-table').DataTable( {
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
                   url:"<?php echo route('cut_pieces.list'); ?>",
                   data:
                   
                   
                   function(data){

          // Append to data
          data.fabric_type = $('select[name="fabric_type"]').children("option:selected").val();
          data.fabric_color = $('select[name="fabric_color"]').children("option:selected").val();
          data.size = $('select[name="size"]').children("option:selected").val();
       },
                   dataType: "json",
                   // type:"post"
               },
               dom:'<"row"lfB>rtip',
               "createdRow": function( row, data, dataIndex ) {            
                   $(row).attr('data-id', data['id']);
                   $(row).attr('data-quantity', data['quantity']);
                   $(row).attr('data-used', data['quantity_used']);
               },
               "columns": [
                   {"data": "fabric_type","name":"fabric_type.name"},
                   {"data": "fabric_color","name":"fabric_color.name"},
                   {"data": "size","name":"size"},
                   {"data": "pieces","name":"pieces"},
                   {"data": "used_pieces"},
                   {"data": "remaining_pieces"},
               ],"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
               'language': {
                   'searchPlaceholder': "{{trans('Type Pieces to filter ...')}}",
                   'lengthMenu': '_MENU_ {{trans("records per page")}}',
                    "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                    "search":  '{{trans("Search")}}',
                    'paginate': {
                           'previous': '<i class="dripicons-chevron-left"></i>',
                           'next': '<i class="dripicons-chevron-right"></i>'
                   }
               },
               order:[['5','desc']],
               
               'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],


                buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                        download('{{route('export.cut_pieces')}}',
                        
                        {

          // Append to data
          fabric_type: $('select[name="fabric_type"]').children("option:selected").val(),
          fabric_color: $('select[name="fabric_color"]').children("option:selected").val(),
          size :$('select[name="size"]').children("option:selected").val(),
       }
                        
                        );            
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
           
           $('select').each(function(){
              
            if($(this).attr('class') == 'swal2-select'){
                return;
            }

               $(this).select2({
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    placeholder: $(this).attr('error-text'), 
               });
               $(this).change(function(){
                   table.draw();
               });
           })


@endsection