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
                        <label>Fabric Type</label>
                        <div class="input-group" style="width:100%">
                  
                        <input  class="form-control"  value="{{$rate->fabric_type_name}}" disabled>
                     </div>
                     </div>
                  </div>

               <div class="col-md-12" id="fabric_colors">
                  <div class="form-group">
                     <label>Fabric Color</strong> </label>
                     <div class="input-group" style="width:100%">
                  
                     <input  class="form-control"  value="{{$rate->fabric_color_name}}" disabled>
                     </div>
                  </div>
                  
               </div>
               
               <div class="col-md-12">
                  <div class="form-group">
                     <label>Size</label>
                     <div class="input-group" style="width:100%">
                  
                     <input  class="form-control"  value="{{$rate->size_height_width}}" disabled>
                     </div>
                  </div>
                  </div>

               <div class="form-group mt-3">
                  <label style="color:black">Rate*</label>
                  <div class="input-group" style="width:100%">
                     <input class="form-control" value="{{$rate->rate}}">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>

                

               
                
               
              
            </form>
         </div>