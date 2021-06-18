<div class="modal-header">
            <h6 class="modal-title" id="permission_title">Edit Permission</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="permission-form">
               
                <div class="form-group mt-3">
                  <label style="color:black">Permission Name</label>
                  <input hidden value="0" id="permission_id" >
                  <div class="input-group" style="width:100%">
                     <input  class="form-control" value="{{ucwords(str_replace('-',' ',$permission->name))}}" disabled>
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                <div class="form-group mt-3">
                  <label style="color:black">Permission Platform</label>
                  <div class="input-group" style="width:100%">
                     <input  class="form-control" value="{{$permission->guard_name}}" disabled>
                </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

            </form>
         </div>