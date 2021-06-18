<div class="modal-header">
            <h6 class="modal-title" id="permission_title">{{$tailor->name}}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="text-dark">×</span>
            </button>
         </div>
         <div class="modal-body">
            <form id="tailor-form">
               
                <div class="form-group mt-3">
                  <label style="color:black">Name</label>
                  <div class="input-group" style="width:100%">
                     <input disabled class="form-control" value="{{$tailor->name}}">
                  </div>
               </div>
               
               <div class="form-group mt-3">
                  <label style="color:black">Address</label>
                  <div class="input-group" style="width:100%">
                     <input disabled class="form-control" value="{{$tailor->address}}">
                  </div>
               </div>

               <div class="form-group mt-3">
                  <label style="color:black">GST No.</label>
                  <div class="input-group" style="width:100%">
                     <input disabled class="form-control" value="{{$tailor->gst_no}}">
                  </div>
               </div>


               <div class="form-group mt-3">
                  <label style="color:black">Rate with welted edges</label>
                  <div class="input-group" style="width:100%">
                     <input disabled class="form-control" value="{{$tailor->rate_with_welted_edges}}">
                  </div>
               </div>

                

               <div class="form-group mt-3">
                  <label style="color:black">Rate without welted edges</label>
                  <div class="input-group" style="width:100%">
                     <input disabled class="form-control" value="{{$tailor->rate_without_welted_edges}}">
                  </div>
               </div>

                
               
            </form>
         </div>