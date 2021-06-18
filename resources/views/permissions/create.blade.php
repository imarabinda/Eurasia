<div class="modal-header">
            <h6 class="modal-title" id="permission_title">Create Permission</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="permission-form">
               
                <div class="form-group mt-3">
                  <label style="color:black">Permission Name*</label>
                  <input hidden value="0" id="permission_id" >
                  <div class="input-group" style="width:100%">
                     <input type="text" for="permisison" verify-ignore="false" class="form-control" error-text="Enter Permission name..." placeholder="Permission Name" name="permission_name">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                <div class="form-group mt-3">
                  <label style="color:black">Permission Platform*</label>
                  <div class="input-group" style="width:100%">
                    
                <select error-text="Select platform" id="permission_platform" name="permission_platform" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                    <option></option>
                           @foreach($platforms as $key =>$platform)
                           <option value="{{$key}}">{{$platform}}</option>
                           @endforeach
                    </select>
                </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

               
               <p class="italic" style="color:red" id="permission_message"></p>
               <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary permission-button-create">{{trans('Create')}}</button>
               </div>
            </form>
         </div>