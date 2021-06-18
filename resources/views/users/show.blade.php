@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$user->first_name}} {{$user->last_name}}</h4>
         
         @can('edit-'.str_replace(' ','-',strtolower($user_role)))
         <a href="{{route('users.edit',$user->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit User</a>
         @endcan

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
                        <label> First Name</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$user->first_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Last Name</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$user->last_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$user->email}}">
                          </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Phone</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$user->phone}}">
                          </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Role</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$user_role}}">
                           
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  
               </form>

               <div class="col-md-12">
                     <div class="form-group">
                     @php
                      
                        $welcome_email = false;   
                        $verification_email = false;   
                        $password_reset = false;
                        $ban = false; 
                        $delete = false;
                        $invisible_login = false;

                     @endphp
                  @can('auth-resend-verification-email-'.$user_role)
                    @if(!$user->email_verified_at )
                    @php
                     $verification_email = true;   
                    @endphp

                    <a href="javascript:void(0)" class="btn btn-info btn-action email-verification"><i class="dripicons-edit"></i> Resend Email Verification</a>
                    @endif
                    @endcan

                  @can('auth-resend-welcome-email-'.$user_role)
                    @if(!$user->password )
                    @php
                     $welcome_email = true;   
                    @endphp
                    <a href="javascript:void(0)" class="btn btn-info btn-action welcome-email"><i class="dripicons-edit"></i> Resend Welcome Email</a>
                    @endif
                    @endcan
                    
                  @can('auth-send-password-reset-link-'.$user_role)
                  @if($user->is_active == 1 && $user->password)
                  @php
                     $password_reset = true;   
                     @endphp
                    <a href="javascript:void(0)" class="btn btn-info btn-action password-reset"><i class="dripicons-edit"></i> Send Reset Password Link</a>
                    @endif
                    @endcan
                    
                    @can('auth-ban-'.$user_role)
                    @if($user->id != Auth::user()->id)
                    @php
                        $ban = true;   
                        @endphp
                       <a href="javascript:void(0)" class="btn btn-info btn-action ban" data-href="{{route('users.ban',$user->id)}}"><i class="dripicons-edit"></i> @if($user->is_active==0) Un-Ban User @else Ban User @endif</a>
                       @endif
                       @endcan
                       
                       @can('auth-invisible-login-'.$user_role)
                       @if($user->id != Auth::user()->id)
                       @php
                        $invisible_login = true;   
                        @endphp
                       <a href="javascript:void(0)" class="btn btn-info btn-action invisible-login" data-href="{{route('users.invisible_login',$user->id)}}"><i class="dripicons-edit"></i> Login to this account</a>
                       @endif
                       @endcan
                       
                       
                  @can('auth-delete-'.$user_role)
                    @if($user->id != Auth::user()->id )
                               @php
                        $delete = true;  
                        
                        echo Form::open(["route" => ["users.destroy", $user->id], "method" => "DELETE"] );
                        echo '<button type="submit" class="mt-2 btn btn-info delete-button" ><i class="fa fa-trash"></i> '.trans("Delete User").'</button> ';
                        echo Form::close();
                       
                       @endphp
                     
                       @endif
                       @endcan


                    
                </div>

            
            
               </div>
               



            </div>
         </div>
      </div>
   </div>






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

@if($ban)


           $(document).on('click','.ban',function(e){
               e.preventDefault();
            var link =$(this).attr('data-href');
            var item = $(this);
swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, do it!',
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
                          $(item).text(response.text + ' user');
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
@endif

@if($invisible_login)


           $(document).on('click','.invisible-login',function(e){
               e.preventDefault();
            var link =$(this).attr('data-href');
            var item = $(this);
swal({
                title: 'Are you sure?',
                text: "You will be logged out from current account!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, login!',
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
                           window.location.reload();
                       }
                   }
               });
                
            }, function (dismiss) {
                
                if (dismiss === 'cancel') {
       
                }
            });
               
           });
@endif

@if($delete)

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