@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">{{$title}}</h4>
         @can('tailor-create')
         <a href="javascript:void(0)" class="btn btn-info btn-action create-tailor"><i class="dripicons-plus"></i> {{__('Add Tailor')}}</a>
        @endcan
        </div>
   </div>
</div>
<div class="row">
   <div class="col-12 mt-2">
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            

            <table id="tailors-table" class="table table-bordered" style="width: 100%">
               <thead>
                  <tr>
                     <th>{{trans('Name')}}</th>
                     <th>{{trans('Adress')}}</th>
                     <th>{{trans('GST no.')}}</th>
                     <th>{{trans('Rate With welted edges')}}</th>
                     <th>{{trans('Rate Without welted edges')}}</th>
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




<div id="tailor-modal" tabindex="-1" role="dialog" aria-labelledby="roll" aria-hidden="true" class="modal fade text-left">
   <div role="document" class="modal-dialog" style="max-width:250px!important">
      <div class="modal-content" id="tailor_modal_content">
         
      </div>
   </div>
</div>
        
@endsection

@section('script')
   

var submit_button_selector = '';
@can('tailor-create')
         
    $(document).on('click','.create-tailor',function(e){
        e.preventDefault();
        $.get('{{route('tailors.create')}}',{},function(response){
            $("#tailor_modal_content").html(response);
            $("#tailor-modal").modal('show');
            
            submit_button_selector = $('.tailor-button-create');
      init_listeners();
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



    @can('tailor-delete')
         
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

@can('tailor-edit')
         
$(document).on('click','.edit-tailor',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.get(url,{},function(response){
            $("#tailor_modal_content").html(response);
            $("#tailor-modal").modal('show');
            
            submit_button_selector = $('.tailor-button-edit');
      init_listeners();
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

@can('tailor-view')
$(document).on('click','.view-tailor',function(e){
        e.preventDefault();
        url = $(this).attr('href');
        $.get(url,{},function(response){
            $("#tailor_modal_content").html(response);
            $("#tailor-modal").modal('show');
            
        });
    });
    @endcan

@can('tailor-create')
         
    $(document).on('click','.tailor-button-create',function(e){
        e.preventDefault();

        if(validate(false)){
            return;
        }
        $.LoadingOverlay("show");
                
        var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#tailor-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                  
        $.ajax({
                          type:'POST',
                          url:'{{route('tailors.store')}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                              $.LoadingOverlay("hide");
                                if(response.success){
                                                                                                    $("#tailor_modal_content").html('');
                                $("#tailor-modal").modal('hide');
                                table.draw();

            
                                    
swal({
                                             title: response.title,
                                             text: response.subtitle,
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


function validate(show = true) {
         //errors
         var has_error = false;
         
        $("input[type='file']").each(function(){
           if($(this).attr('verify-ignore') != 'true' && $(this).attr('multiple') == 'multiple' && $(this).val() == ''){
            if(show){
               showError($(this).parent().parent(),this);
            }
               has_error = true;
            }
        });
   
         $("input[type='text']").each(function(){
             if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  && $(this).val() == ''){
                 has_error = true;
                 if(show){
                    showError($(this).parent().parent(),this); 
                 }       
             }
         });
      
         $("input[type='number']").each(function(){
             if($(this).val() == ''){
                 has_error = true;
                 if(show){showError($(this).parent().parent(),this);
             }
            }
             if($(this).val() == 0){
                 has_error = true;
                 
            
                 if(show){
                 showError($(this).parent().parent(),this);
             }
            }
         });
         
   
         
         $('select').each(function() {
           if($(this).attr('verify-ignore') != 'true'){
               if($(this).val() == ''){   
                   has_error = true;
                   if(show){
                   showError($(this).parent(),this);
               }
            }
        }

        
         });
            
         if(!has_error){
            showButton();
         }else{
            hideButton();
         }

         return has_error;   
      }
      

    @can('tailor-edit')
         
    $(document).on('click','.tailor-button-edit',function(e){
        e.preventDefault();

        
        if(validate(false)){
            return;
        }

        $.LoadingOverlay("show");
                
        var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#tailor-form").serializeArray();
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
                                                                                                    $("#tailor_modal_content").html('');
                                $("#tailor-modal").modal('hide');
                                table.draw();
                                    
swal({
                                             title: response.title,
                                             text: response.subtitle,
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
                    $(this).val(Math.abs($(this).val()).toFixed(2));
                }
                
                $(this).focusout(function(){
                    
                if($(this).attr('type') == 'number'){
                    $(this).val(Math.abs($(this).val()).toFixed(2));
                }

                });

               $(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent().parent());
                   }
                   $('#tailor_message').html('');
               });

               $(this).change(function() {
                
                if($(this).attr('name') == 'name'){
                     var str = $(this).val();  
                    str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                        return letter.toUpperCase();
                     });
                     $(this).val(str);
                }


                  validate(false);
               });

               $(this).keyup(function() {


                if($(this).attr('name') == 'name'){
                     var str = $(this).val();  
                    str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                        return letter.toUpperCase();
                     });
                     $(this).val(str);
                }
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


function hideError(element,main=null){
      if(!main){
         main = element;
      }
      $(element).removeClass('has-error');
      var message_box = $(element).find('.validation-msg');
      $(message_box).html('');
   }
   //error show
   function showError(element,main=null,message =null){
      if(!main){
          main = element;
      }
      $(element).addClass('has-error');
      var message_box = $(element).find('.validation-msg');
      if(!message){
         message = $(main).attr('error-text');
      }
      $(message_box).html(message);
   }


@can('tailor-manage')
         
               var table = $('#tailors-table').DataTable( {
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
                   url:"<?php echo route('tailors.list'); ?>",
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
                   {"data": "address"},
                   {"data": "gst_no"},
                   {"data": "rate_with_welted_edges"},
                   {"data": "rate_without_welted_edges"},
                   {"data": "options"},
               ],
               'language': {
                   'searchPlaceholder': "{{trans('Type name , address or gst no to filter ...')}}",
                   'lengthMenu': '_MENU_ {{trans("records per page")}}',
                    "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                    "search":  '{{trans("Search")}}',
                    'paginate': {
                           'previous': '<i class="dripicons-chevron-left"></i>',
                           'next': '<i class="dripicons-chevron-right"></i>'
                   }
               },
               order:[['5','desc']],
               "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
      buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                         download('{{route('export.tailors')}}',{
                            
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