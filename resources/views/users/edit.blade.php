@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$user->first_name}} {{$user->last_name}}</h4>
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
                        <label> First Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter first name..." placeholder="First name" name="first_name" id="first_name" value="{{$user->first_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Last Name*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter last name..." placeholder="Last name" name="last_name" id="last_name" value="{{$user->last_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Email*</label>
                        <div class="input-group">
                           <input type="email" required class="form-control" error-text="Enter email..." placeholder="Email" id="email" name="email" value="{{$user->email}}">
                          </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Phone*</label>
                        <div class="input-group">
                           <input type="number" required class="form-control" error-text="Enter phone number..." placeholder="Phone Number" id="phone" name="phone" value="{{$user->phone}}">
                          </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Role*</label>
                        <div class="input-group">
                        
                        <select class="form-control select2 mb-3 custom-select js-states" error-text="Select role..." id="role" name="role" placeholder="Select products">
                              <option></option>
                           
                            @foreach($permitable_roles as $role)
                            <option @if($user_role == $role->name) selected @endif value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>

                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>


                  <div class="col-md-12">
                     <div class="form-group">
                        @php
                      
                      $welcome_email = false;   
                      $verification_email = false;   
                      $password_reset = false; 
                     @endphp
                  @can('auth-resend-verification-email-'.$user_role)
                    @if(!$user->email_verified_at)
                    
                    @php
                     $verification_email = true;   
                    @endphp

                    <a href="javascript:void(0)" class="btn btn-info btn-action email-verification"><i class="dripicons-edit"></i> Resend Email Verification</a>
                    @endif
                    @endcan

                  @can('auth-resend-resend-welcome-email-'.$user_role)
                    @if(!$user->password )            
                    @php
                     $welcome_email = true;   
                    @endphp
                    <a href="javascript:void(0)" class="btn btn-info btn-action welcome-email"><i class="dripicons-edit"></i> Resend Welcome Email</a>
                    @endif
                    @endcan
                    
                  @can('auth-send-password-reset-link-'.$user_role)
                    @if($user->is_active == 1)@php
                     $password_reset = true;   
                    @endphp
                    <a href="javascript:void(0)" class="btn btn-info btn-action password-reset"><i class="dripicons-edit"></i> Send Reset Password Link</a>
                    @endif
                  @endcan
                    </div>
                </div>

            
                
      <div class="col-md-12" id="button">
                  <div class="form-group text-left">
                     <input type="button" value="{{trans('Update User')}}" id="submit-btn" class="btn btn-primary">
                  </div>
               </div>

               
               </div>
               



            </div>
         </div>
      </div>
   </div>






</form>
@endsection

@section('script')

@if($password_reset)
                    
$('.password-reset').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
                         
        $.ajax({
            url: "{{route('password.email')}}",
            type:'POST',
            processData: false,
            contentType: false,              
            data:{
                email:'{{$user->email}}'
            },
            success:function(response){
                $.LoadingOverlay("hide");
                if(response.success){
                   swal({
                                             title: 'Password reset link sent successfully!',
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
                                                }
                                    });
                }
            }
        });
    });

    @endif

@if($verification_email)
                    
    $('.email-verification').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
                         
        $.ajax({
            url: "{{route('users.email.verification',$user->id)}}",
            type:'POST',
            processData: false,
            contentType: false,              
            
            success:function(response){
                $.LoadingOverlay("hide");
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
                                                }
                                    });
            }
        });
    });
@endif


@if($welcome_email)
                    
    $('.welcome-email').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
                         
        $.ajax({
            url: "{{route('users.email.welcome',$user->id)}}",
            type:'POST',
            processData: false,
            contentType: false,              
            
            success:function(response){
                $.LoadingOverlay("hide");
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
                                                }
                                    });
            }
        });
    });
@endif

   
$('select').each(function(){
   if($(this).attr('class') == 'swal2-select'){
                return;
            }

    $(this).select2({
        minimumResultsForSearch: Infinity,
    placeholder: $(this).attr('error-text'),
    
    });
});

    function individual_input(input){
          $(input).on('change', function (e) {
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
               }
               else{
                  $(this).val(val);
               }
               
               validate(false);
               // validate_with_final_stock(this,false);
            });
            
            $(input).on('keyup',function(){
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
               }
               else{
                  $(this).val(val);
               }
               validate(false);
            });
            
            $(input).on('focus',function(){
               hideError($(this).parent(),this);
               $("#product_message").html('');
            });
    }
   
   //submit
   $('#submit-btn').on("click", function (e) {
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
                          url:'{{route('users.update',$user->id)}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                             $.LoadingOverlay("hide");
                            if(response.success){
                               swal({
                                             title: 'User updated successfully!',
                                             text: 'redirecting to manage users.',
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
                                                      location.href = response.redirect;      
                                                }
                                    });
                            }else{
                                $(response.element).focus();
                                $("#product_message").html(response.message);
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
          });
             
   //validation part
   function validate(show = true) {
   
      //errors
      var has_error = false;

      $("input[type='text']").each(function(){
          if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  &&  $(this).val() == ''){
              has_error = true;
              if(show){
                 showError($(this).parent().parent(),this);        
              }
          }
      });

      $('input[type="number"]').each(function(){
          if($(this).attr('name')=='phone'){

        var phoneno = $(this).val();
        var phoneno_schema = /^\d{10}$/;
        if(!phoneno.match(phoneno_schema))
        {
            has_error =  true;
            if(show){
                 showError($(this).parent().parent(),this);        
              }
        }
          }
      });

      

      $('input[type="email"]').each(function(){
          if($(this).attr('name')=='email'){

        var email = $(this).val();
        var email_format = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        if(!email.match(email_format))
        {
            has_error =  true;
            if(show){
                 showError($(this).parent().parent(),this);        
              }
        }
          }
      });

      
        
      if(!$('#role').val()){
          has_error = true;
      }


         if(!has_error){
            showButton();
         }else{
            hideButton();
         }

      return has_error;     
   }
   
   $('#role').on('select2:select', function (e) {
       validate(false);
});

   //error hide
   function hideError(element,main=null){
      if(!main){
          main = element;
      }
      $(element).removeClass('has-error');
      var message_box = $(element).find('.validation-msg');
      $(message_box).html('');
   }
   
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
               
               if($(this).attr('type') == 'number'){
                  if($(this).attr('reference-number') != '' ){
                      individual_input($(this));
                     }
                  return;
               }
               
               $(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent());
                   }
                   $('#product_message').html('');
               });

               $(this).change(function() {
                  validate(false);
               });

               $(this).keyup(function() {
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

   
@endsection