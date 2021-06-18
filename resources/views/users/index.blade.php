
@extends('template.index')
@section('content')

    <div class="row">
       <div class="col-md-12">
        <div class="page-title-box">
            @can('create-user')
                <a href="{{route('users.create')}}" class="btn btn-info btn-action"><i class="dripicons-plus"></i> {{__('Create New User')}}</a>
            @endcan
        </div>
        </div>
    </div>


<div class="row">



    <div style="width:10%;padding-left:15px">
                     <div class="form-group">
                        <select error-text="Select role" required name="role" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option value="0">All</option>
                                              
                           @foreach($permitable_roles as $value)
                           <option value="{{$value}}">{{$value}}</option>
                           @endforeach

                        </select>
                     </div>
                  </div>

                                <div class="col-12">
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="user-data-table" class="table table-bordered" style="width: 100%">
                                                       
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 

@endsection
@section('script')


               var userTable = $('#user-data-table').DataTable( {
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
                   url:"<?php echo route('users.list'); ?>",
                   data:
                              
                   function(data){

          data.role = $('select[name="role"]').children("option:selected").val();
       
                },
                   dataType: "json",
                   // type:"post"
               },
               order:[[5,'desc']],
               dom:'<"row"lfB>rtip',
                "createdRow": function( row, data, dataIndex ) {            
                   },
        "columns": [
          
                   {"title":"First Name","data": "first_name"},
                   {"title":"Last Name","data": "last_name"},
                   {"title":"Email","data": "email"},
                   {"title":"Role","data": "role",'orderable':false,'searchable':false},
                   {"title":"Registered On","data": "created_at"},
                   {"title":"Action","data": "options"},
                   ],


                   buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                        download('{{route('export.users')}}',{

          // Append to data
          role: $('select[name="role"]').children("option:selected").val(),
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

               
               'language': {
                   'searchPlaceholder': "{{trans('Type Name, email or role name ...')}}",
                   'lengthMenu': '_MENU_ {{trans("records per page")}}',
                    "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                    "search":  '{{trans("Search")}}',
                    'paginate': {
                           'previous': '<i class="dripicons-chevron-left"></i>',
                           'next': '<i class="dripicons-chevron-right"></i>'
                   }
               },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
               'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
               
           } );

           $(document).on('click','.ban',function(e){
               e.preventDefault();
            var link =$(this).attr('data-href');
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
                
                $.ajax({
                   url:link,
                   type:'POST',
                   success:function(response){
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
                                                userTable.draw();
                                                }
                                    });
                       }
                   }
               });
                
            }, function (dismiss) {
                
                if (dismiss === 'cancel') {
       
                }
            });
               
           });

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
                   userTable.draw();
               });
           })


           
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


@endsection