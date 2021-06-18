@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Edit Profile</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
      </div>
   </div>
</div>

<form id="user-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body ">
               
                <div class="row form-group">
                  

               
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>New Password</label>
                        <div class="input-group">
                           <input type="text" verify-ignore="true" class="form-control" id="new_password" error-text="Enter new password..." placeholder="New Password" name="new_password">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  

                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="input-group">
                           <input type="text" class="form-control" verify-ignore="true" id="confirm_password" error-text="Enter confirm password..." placeholder="Confirm Password" name="confirm_password">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

            </div>
         </div>
      </div>
   </div>




   

      <div class="col-md-12" id="button">
                  <div class="form-group">
                     <input type="button" value="{{trans('Update Profile')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>

      </div>
   </div>



</form>
@endsection

@section('script')

    
   //validation part
   function validate(show = true) {
   
      //errors
      var has_error = false;
    
    $('input').each(function(){
        

        if($(this).attr('type') == 'hidden' || $(this).attr('type') == 'button'){
            return ; 
        }
        if($(this).val() == ''){
            has_error = true;
          }else{
              has_error = false
          }

           if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  &&  $(this).val() == ''){
              has_error = true;
              if(show){
                 showError($(this).parent().parent(),this);        
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
   
 

      var submit_button_selector = $("#submit-btn");

      init_listeners();
      function init_listeners(){
            hideButton();

            
            $("textarea").change(function(){
               validate(false);
            });

            $("textarea").keyup(function(){
               validate(false);
            });
            

            $("input").each(function(){
               
               
               $(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent().parent());
                   }
               });

               
               if($(this).attr('name') == 'new_password' || $(this).attr('name') == 'confirm_password'){
                   $(this).focusout(function(){
                        $(this).attr('type','password');
                    });
                   
                   
               }


               $(this).change(function() {
                   
                if($(this).attr('name') == 'new_password'){
                    if($(this).val() != ''){
                        $('input[name="confirm_password"]').attr('verify-ignore',false);
                   }else{
                        $('input[name="confirm_password"]').attr('verify-ignore',true);
                    }
                     if($(this).val() == ''){
                        $(this).attr('type','text');
                    }
                    validate(false);
                   return;
                }
                
                 if($(this).attr('name') == 'confirm_password'){
                        if($('input[name="new_password"]').val() != ''){
                        $(this).attr('verify-ignore',false);
                   }else{
                        $(this).attr('verify-ignore',true);
                    }
                     if($(this).val() == ''){
                        $(this).attr('type','text');
                    }
                    validate(false);
                    return;
                }
                
                    
                  validate(false);
               });

               $(this).keyup(function() {

                   if($(this).attr('name') == 'new_password'){
                        if($(this).val() != ''){
                        $('input[name="confirm_password"]').attr('verify-ignore',false);
                   }else{
                        $('input[name="confirm_password"]').attr('verify-ignore',true);
                    }
 if($(this).val() == ''){
                        $(this).attr('type','text');
                    }
                    validate(false);
                     return;
                    }  

                 if($(this).attr('name') == 'confirm_password'){
                        if($('input[name="new_password"]').val() != ''){
                        $(this).attr('verify-ignore',false);
                   }else{
                        $(this).attr('verify-ignore',true);
                    }
                     if($(this).val() == ''){
                        $(this).attr('type','text');
                    }
                    validate(false);
                     return;
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

      submit_button_selector.click(function(e){
          e.preventDefault();


          if (!validate()) {
                 $.LoadingOverlay("show");
                      var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#user-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                      $.ajax({
                          type:'POST',
                          url:'{{route('profile.update')}}',
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
                            }else{
                                $(response.element).focus();
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
              }


      })
   

      
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

@endsection