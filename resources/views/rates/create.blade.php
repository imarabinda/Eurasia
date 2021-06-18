<div class="modal-header">
            <h6 class="modal-title" id="permission_title">Add New Tailor</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="rate-form">
               
                


                <div class="col-md-12">
                     <div class="form-group">
                        <label>Fabric Type*</label>
                        <select error-text="Select fabric type" relation="fabric_colors" required name="fabric_types" required class="select2 mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option></option>
                           @foreach($fabric_types as $fabric_type)
                           <option value="{{$fabric_type->id}}" is-relation="@php if(count($fabric_type->colors) > 0) echo true; @endphp">{{$fabric_type->name}}</option>
                           @endforeach
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-12" id="fabric_colors"> 
                     <div class="form-group">
                        <label>Fabric Color</label>
                        <select verify-ignore="true" name="fabric_colors" class="select2 mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option>Any Color</option>
                           </select>
                        <span class="validation-msg" ></span>
                     </div>   
                  </div>


                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Size</label>
                        <select verify-ignore="true" name="size" class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;">
                           <option>Any Size</option>
                           @foreach($sizes as $size)
                           <option value="{{$size->id}}" >{{$size->height}} x {{$size->width}}</option>
                           @endforeach
                        </select>
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

               <div class="form-group mt-3">
                  <label style="color:black">Rate*</label>
                  <div class="input-group" style="width:100%">
                     <input type="number" for="rate" verify-ignore="false" class="form-control" error-text="Enter Rate..." placeholder="Rate" name="rate">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                

               
                
               
               <p class="italic" style="color:red" id="rate_message"></p>
               <div class="form-group text-center">
                  <button type="submit" class="btn btn-primary rate-button-create">{{trans('Add')}}</button>
               </div>
            </form>
         </div>