@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Available roles</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>Filter roles using platform </small></p>
         @can('role-create')
         <a href="{{route('roles.create')}}" class="btn btn-info btn-action create-role"><i class="dripicons-plus"></i> {{__('Create Role')}}</a>
        @endif
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
                        <select error-text="Select platform" required name="platform" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option value="0">All</option>
                                              
                           @foreach($platforms as $platform => $value)
                           <option value="{{$platform}}">{{$platform}}</option>
                           @endforeach

                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


                  

            </div>
    

            <table id="roles-table" class="table table-bordered" style="width: 100%">
               <thead>
                  <tr>
                     <th>{{trans('Name')}}</th>
                     <th>{{trans('Platform')}}</th>
                     <th>{{trans('Action')}}</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <!-- end col -->
</div>
<!-- end row -->




<div id="role-modal" tabindex="-1" role="dialog" aria-labelledby="roll" aria-hidden="true" class="modal fade text-left">
   <div role="document" class="modal-dialog" style="max-width:250px!important">
      <div class="modal-content" id="role_modal_content">
         
      </div>
   </div>
</div>
        
@endsection

@section('script')
   
@can('role-manage')
         
               var table = $('#roles-table').DataTable( {
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
                   url:"<?php echo route('roles.list'); ?>",
                   data:
                   
                   
                   function(data){

          // Append to data
          data.platform = $('select[name="platform"]').children("option:selected").val();
       },
                   dataType: "json",
                   // type:"post"
               },
               dom:'<"row"lfB>rtip',
               "createdRow": function( row, data, dataIndex ) {            
                   $(row).attr('data-name', data['name']);
               },
               order:[[2,'desc']],
               "columns": [
                   {"data": "name"},
                   {"data": "guard_name"},
                   {"data": "options"},
               ],
               'language': {
                   'searchPlaceholder': "{{trans('Type name or platform to filter ...')}}",
                   'lengthMenu': '_MENU_ {{trans("records per page")}}',
                    "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                    "search":  '{{trans("Search")}}',
                    'paginate': {
                           'previous': '<i class="dripicons-chevron-left"></i>',
                           'next': '<i class="dripicons-chevron-right"></i>'
                   }
               },
               order:[['2']],
               
               'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
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
           });

           
$(document).on('click','.delete-button',function(e){
   	    e.preventDefault();
   	    var item = $(this).parent();
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
                $(item).submit();   
            }, function (dismiss) {
                
                if (dismiss === 'cancel') {
       
                }
            });
 
});

@endif

@endsection