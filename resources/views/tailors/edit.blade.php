<div class="modal-header">
            <h6 class="modal-title" id="permission_title">Edit - {{$tailor->name}}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="tailor-form">
               
                <div class="form-group mt-3">
                  <label style="color:black">Name*</label>
                  <div class="input-group" style="width:100%">
                     <input type="text" for="name" verify-ignore="false" class="form-control" error-text="Enter name..." placeholder="Name" name="name" value="{{$tailor->name}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               
               <div class="form-group mt-3">
                  <label style="color:black">Address</label>
                  <div class="input-group" style="width:100%">
                     <input type="text" for="address" verify-ignore="true" class="form-control" error-text="Enter address..." placeholder="Address" name="address" value="{{$tailor->address}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

               <div class="form-group mt-3">
                  <label style="color:black">GST No.</label>
                  <div class="input-group" style="width:100%">
                     <input type="text" for="gst_no" verify-ignore="true" class="form-control" error-text="Enter GST No. ..." placeholder="GST No." name="gst_no" value="{{$tailor->gst_no}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>


               <div class="form-group mt-3">
                  <label style="color:black">Rate with welted edges*</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" for="rate_with" verify-ignore="false" class="form-control" error-text="Enter Rate with welted edges..." placeholder="Rate with welted edges" name="rate_with_welted_edges" value="{{$tailor->rate_with_welted_edges}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                

               <div class="form-group mt-3">
                  <label style="color:black">Rate without welted edges*</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" for="rate_without" verify-ignore="false" class="form-control" error-text="Enter Rate without welted edges..." placeholder="Rate without welted edges" name="rate_without_welted_edges" value="{{$tailor->rate_without_welted_edges}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                
               
               <p class="italic" style="color:red" id="tailor_message"></p>
               <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary tailor-button-edit">{{trans('Update')}}</button>
               </div>
            </form>
         </div>