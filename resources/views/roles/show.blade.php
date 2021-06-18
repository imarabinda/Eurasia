@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$role->name}}</h4>
         @can('roles-auth-edit-'.$role_name)
            <a href="{{route('roles.edit', $role->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit Role</a>
         @endcan
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
                        <label>Role Name</label>
                        <div class="input-group">
                           <input class="form-control" disabled value="{{$role->name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Platform</label>
                        <div class="input-group">
                        <input class="form-control" disabled value="{{$role->guard_name}}">
                        
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



@can('roles-auth-view-permission-'.$role_name)
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
         <h4 class="page-title font-16">Permissions: </h4>
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
                                                                  <input type="checkbox" disabled  checked class="custom-control-input" id="customCheck{{$id}}" name="permissions[{{$id}}]" value="{{$id}}" data-parsley-multiple="groups" data-parsley-mincheck="2">
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

         
   
      </div>
      
      @endcan

</form>
@endsection