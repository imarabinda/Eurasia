@extends('template.index')

@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Create New Role</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
      </div>
   </div>
</div>
<form id="role-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Role Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" id="name" error-text="Enter role name..." placeholder="Role Name" name="name">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Platform *</label>
                        <div class="input-group">
                        
                <select error-text="Select platform" id="guard_name" name="guard_name" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                    <option></option>
                           @foreach($platforms as $key =>$platform)
                           <option value="{{$key}}">{{$key}}</option>
                           @endforeach
                    </select>   
                        
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
               
            </div>
         </div>
      </div>
      
   


      
      
   </div>



@can('permission-assign-to-roles')

   <div class="row">
      <div class="col-lg-12">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-12">
                  

                     <div class="form-group mb-0 row">
                        @php
                        $output =[];
                        foreach($permissions as $id => $permission){
                           
                           foreach($permissionsLists as $name){
                              
                              $explodePermission = explode('-',$permission); 
                              $explodeName = explode('-',$name);
                              $matched = true; 
                              $t = [];
                              
                              for($i=0;$i < count($explodeName);$i++){
                                 if($matched == true && array_key_exists($i,$explodePermission) && $explodeName[$i] == $explodePermission[$i]){
                                    $matched = true;
                                 }else{
                                    $matched = false;
                                 }                               
                              }

                              if($matched){   
                                 $output[$name][$id] = $permission;
                              }
                           }
                        }                 
                        $output['others'] = array_diff($permissions,data_get($output,'*.*'));
                        
                        @endphp

<div class="col-md-3" >
         <h4 class="page-title font-16">Set Permissions: </h4>
</div>
                  @foreach($output as $item => $permissions)
                     @empty($permissions)
                        @continue
                     @endempty
                                       <label style="font-weight:800" class="col-md-12 my-2 control-label">{{ucwords(str_replace('-',' ',$item))}}</label>
                                       <div class="col-md-12">
                                          @foreach($permissions as $id=>$permission)
                                                            <div class="form-check-inline my-2">
                                                               <div class="custom-control custom-checkbox">
                                                                  <input type="checkbox" class="custom-control-input" id="customCheck{{$id}}" name="permissions[{{$id}}]" value="{{$id}}" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                                                  <label class="custom-control-label" for="customCheck{{$id}}">{{ucwords(str_replace('-',' ',str_replace($title,'',$permission)))}}</label>
                                                               </div>
                                                            </div>
                                                            @endforeach
                                                    </div>
                  @endforeach

                  


                           </div>
                                                    </div>
                  
                  </div>
                  
               </div>
               
            </div>
         </div>
@endcan
         
      <div class="col-md-12" id="button">
                  <div class="form-group text-center">
                     <input type="button" value="{{trans('Create Role')}}" id="submit-btn" class="btn btn-primary">
                  </div>
      </div>
   
      </div>
      
      

</form>
@endsection
@section('script')
      


            var submit_button_selector = $("#submit-btn");

            submit_button_selector.click(function(e){
               e.preventDefault();
               
                             $.LoadingOverlay("show");
               var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#role-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                                            
                      $.ajax({
                          type:'POST',
                          url:'{{route('roles.store')}}',
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
                                                   if(response.redirect){
                                                location.href = response.redirect;
                                                }
                                                }
                                    });
                            }
                          },
                          
                          error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
                      });
                  
            });


      init_listeners();
       function init_listeners(){
              
            
            hideButton();
           $("select").each(function(){
               if($(this).attr('class') == 'swal2-select'){
                return;
            }   
            $(this).change(function() {
                  if($(this).attr('verify-ignore') != 'true' ){
                     validate(false);
                     hideError($(this).parent(),this);
                  }
               });
   
                 $(this).select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                placeholder: $(this).attr('error-text'), 
               });
   
               

           $("input").each(function(){
              $(this).change(function() {
                    validate(false);
                    
              if($(this).attr('name') =='name'){
               var str = $(this).val();  
               str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                        return letter.toUpperCase();
                     });
                     $(this).val(str);
              }
                    hideError($(this).parent(),this);
                 });
                 
                 $(this).keyup(function() {
                    validate(false);
                    
              if($(this).attr('name') =='name'){
               var str = $(this).val();  
               str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
                        return letter.toUpperCase();
                     });
                     $(this).val(str);
              }
                    hideError($(this).parent(),this);
                });
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
            function validate(show=true){
               var has_error = false;
         $("input[type='text']").each(function(){
             if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  && $(this).val() == ''){
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
      function showError(element,main=null,message=null){
         if(!main){
             main = element;
         }
         $(element).addClass('has-error');
         var message_box = $(element).find('.validation-msg');
         if(!message){
            $(main).attr('error-text')
         }
         $(message_box).html(message);
      }
      


@endsection