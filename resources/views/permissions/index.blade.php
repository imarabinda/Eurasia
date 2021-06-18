@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Available Permissions</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>Filter permissions using platform </small></p>
         @can('permission-create')
         <a href="javascript:void(0)" class="btn btn-info btn-action create-permission"><i class="dripicons-plus"></i> {{__('Add Permission')}}</a>
        @endcan
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
                                              
                           @foreach($platforms as $platform)
                           <option value="{{$platform}}">{{$platform}}</option>
                           @endforeach

                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


                  

            </div>
    

            <table id="permissions-table" class="table table-bordered" style="width: 100%">
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




<div id="permission-modal" tabindex="-1" role="dialog" aria-labelledby="roll" aria-hidden="true" class="modal fade text-left">
   <div role="document" class="modal-dialog" style="max-width:250px!important">
      <div class="modal-content" id="permission_modal_content">
         
      </div>
   </div>
</div>
        
@endsection

@section('script')
   

@can('permission-create')
         
    $(document).on('click','.create-permission',function(e){
        e.preventDefault();
        $.get('{{route('permissions.create')}}',{},function(response){
            $("#permission_modal_content").html(response);
            $("#permission-modal").modal('show');
            
            $('select').each(function(){
                            if($(this).attr('class') == 'swal2-select'){
                return;
            }

                $(this).select2({
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    placeholder: $(this).attr('error-text'), 
                });
            });
            
        });
    });

    @endcan
    var url = '';



    @can('permission-delete')
         
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
    
@endcan
@can('permission-edit')
         
$(document).on('click','.edit-permission',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.get(url,{},function(response){
            $("#permission_modal_content").html(response);
            $("#permission-modal").modal('show');
            $('select').each(function(){
                            if($(this).attr('class') == 'swal2-select'){
                return;
            }

               $(this).select2({
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    placeholder: $(this).attr('error-text'), 
               });
           });

        });
    });
    @endcan

@can('permission-view')
$(document).on('click','.view-permission',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.get(url,{},function(response){
            $("#permission_modal_content").html(response);
            $("#permission-modal").modal('show');
            
        });
    });
    @endcan

@can('permission-create')
         
    $(document).on('click','.permission-button-create',function(e){
        e.preventDefault();

        $.LoadingOverlay("show");
                
        var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#permission-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                  
        $.ajax({
                          type:'POST',
                          url:'{{route('permissions.store')}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                              $.LoadingOverlay("hide");
                                if(response.success){
                                                                                                    $("#permission_modal_content").html('');
                                $("#permission-modal").modal('hide');
                                table.draw();

            
                                    
swal({
                                             title: 'Permission created successfully!',
                                             text: '',
                                             type: 'success',
                                             showCancelButton: false,
                                             confirmButtonClass: 'btn btn-success',
                                             cancelButtonClass: 'btn btn-danger m-l-10',
                                             timer: 1500
                                          }).then(
                                          function(){

                                          }, 
                                          function (dismiss) {
                                                if (dismiss === 'timer') {
                               }
                                    })

            }

        },error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
    });
    });
@endcan



    @can('permission-edit')
         
    $(document).on('click','.permission-button-edit',function(e){
        e.preventDefault();

        $.LoadingOverlay("show");
                
        var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#permission-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });

        var str = url.substr(url.lastIndexOf('/') + 1) + '$';
        url =  url.replace( new RegExp(str), '' );
                  
        $.ajax({
                          type:'POST',
                          url:url+'update',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                              $.LoadingOverlay("hide");
                                if(response.success){
                                                                                                    $("#permission_modal_content").html('');
                                $("#permission-modal").modal('hide');
                                table.draw();
                                    
swal({
                                             title: 'Permission updated successfully!',
                                             text: '',
                                             type: 'success',
                                             showCancelButton: false,
                                             confirmButtonClass: 'btn btn-success',
                                             cancelButtonClass: 'btn btn-danger m-l-10',
                                             timer: 1500
                                          }).then(
                                          function(){

                                          }, 
                                          function (dismiss) {
                                                if (dismiss === 'timer') {
                               }
                                    })

            }

        },error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
    });
    });
    @endcan

@can('permission-manage')
         
               var table = $('#permissions-table').DataTable( {
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
                   url:"<?php echo route('permissions.list'); ?>",
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
               order:[['2','desc']],
               "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
               
               'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
               
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

@endcan

@endsection