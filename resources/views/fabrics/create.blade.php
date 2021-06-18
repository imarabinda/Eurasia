@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Create New Fabric</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
      </div>
   </div>
</div>
<form id="fabric-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Receiving date*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter receiving date..." placeholder="yyyy/mm/dd" name="receiving_date" id="receiving_date">
                           <div class="input-group-append calender-open bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Mill ID*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter mill id..." placeholder="Mill ID" name="mill_id">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Mill Ref ID*</label>
                        <div class="input-group">
                           <input type="text" class="form-control" error-text="Enter mill id ref..." placeholder="Mill Ref ID" name="mill_ref_id">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  <div class="col-md-3">
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
                  <div class="col-md-3" id="fabric_colors" style="display: none">    
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Width*</label>
                        <div class="input-group">
                           <input type="number" class="form-control" error-text="Enter width..." min="0" placeholder="Width" name="width">
                        </div>
                        <span class="validation-msg" ></span>
                        <!-- input-group -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Total quantity*</label>
                        <div class="input-group">
                           <input type="number" class="form-control" error-text="Enter total quantity..." placeholder="Total quantity" min="1" name="total_quantity" id="total_quantity">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-5">
                     <div class="form-group" style="margin-bottom:0!important">
                        <label>Each Roll data*</label>
                        <div class="input-group">
                           <input type="text" verify-ignore="true" name="rolls" class="form-control" placeholder="{{trans('Enter each roll quantity')}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg"></span>
                     </div>
                  </div>
                  <div class="col-md-7 float-right align-self-center" style="display:none; text-align:right" id="remaining_container">    
                     <label>Remaining</label><br>
                     <span id="remaining_quantity"  disabled style="background:grey;color:white;border-radius:10px;width:100%;padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;">0</span>
                  </div>
                  <style>
                     thead>tr{
                     background: #e2e2e2!important
                     }
                  </style>
                  <div class="col-md-12 table-responsive">
                     <p class="italic" style="color:red" id="roll_message"></p>
                     <table style="display:none" id="roll-table" class="table table-hover roll-list">
                        <thead>
                           <tr>
                              <th><i class="dripicons-view-apps"></i></th>
                              <th>{{trans('Roll ID')}}</th>
                              <th>{{trans('Roll Quantity')}}</th>
                              <th><i class="dripicons-trash"></i></th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-md-12">
                     <div class="form-group">
                        <input type="button" value="{{trans('Create')}}" id="submit-btn" class="btn btn-primary">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
   </div>
</form>
@endsection

@section('script')


   
   $('#receiving_date').bootstrapMaterialDatePicker({
      weekStart : 0, 
      time: false ,
      }).on('dateSelected',function(e,data){
       $('.dtp-btn-ok').click();
   });
   
   $('.calender-open').click(
   function(){
           $('#receiving_date').focus();
   
   });
   
   
   $('[data-toggle="tooltip"]').tooltip(); 
   
   
          
      select_listner('fabric_types');
      function select_listner(id){
          $("select[name="+id+"]").change(function(){
              var relation  = $("select[name="+id+"]").attr('relation');
              var relation_avail = $('option:selected',this).attr('is-relation');
              if(relation_avail == 1){
                  fetchAndAppendOptions(relation,$(this).val());
              }else{
                  $('#'+relation).hide(300);
                  nestedRelationHide(relation);
              }
              validate(false);
          });
      }
   
      function nestedRelationHide(id){
          if($('#'+id).length > 0){
              var relation = $('#'+id).find('select').attr('relation');
              $('#'+id).hide(300);
              $('#'+id).html('');
              nestedRelationHide(relation);
          }
      }
   
   function fetchAndAppendOptions(element,id){
      $.post("<?php echo route('relations.switch')?>",{for:element,id:id},function(result,status){
          if(status){
              $('#'+element).show(300);
              $("#"+element).html(result);
              select_listner(element);
              $("select[name="+element+"]").select2({
                  width: '100%',
                  minimumResultsForSearch: Infinity,
                  placeholder: $("select[name="+element+"]").attr('error-text'), 
              });
                      
          }else{
              $('#'+element).hide(300);
          }
          validate(false);
      });
   }
   
   
   //rolls calculation
   $("input[name='rolls']").on('keyup', function (e) {
          var total_quantity = $("#total_quantity").val();
          if (e.key === 'Enter' || e.keyCode === 13) {
           var roll_quantity =  $(this).val();
          
      var value = validate_with_total_quantity(parseInt(total_quantity),roll_quantity);
          if(value > 0){
              column_add_to_table(value);
          }
      }
   });
   
   
   
   $("input[name='rolls']").on("input", function () {
   var total_quantity = $("#total_quantity").val();
   var roll_quantity =  $(this).val().slice(0, -1);
   hideRollMessage();    
   if($(this).val().indexOf(',') > -1) {
      var value = validate_with_total_quantity(parseInt(total_quantity),roll_quantity);
          if(value > 0){
              column_add_to_table(value);
          }
      }
   });
   var remaining_ = 0; 
   
   $("#total_quantity").on('keyup',function(){
      var input_total = 0;
       $('.roll-row').each(function(index){
          var element =$(this).find('input[type="number"]');
          var value = $(element).val();
          input_total = input_total+parseInt(value);
      });
      var extra = parseInt($(this).val()) - input_total;
      update_remaining(extra);
   })
   
   
   $("#total_quantity").on('change',function(){
      var input_total = 0;
       $('.roll-row').each(function(index){
          var element =$(this).find('input[type="number"]');
          var value = $(element).val();
          input_total = input_total+parseInt(value);
      });
      var extra = parseInt($(this).val()) - input_total;
      update_remaining(extra);
   })
   
   
   
   function individual_input(input){
      $(input).on('keyup', function (e) {
          var total_quantity = $("#total_quantity").val();
          var roll_quantity =  $(this).val();
          var value = validate_with_total_quantity(parseInt(total_quantity),roll_quantity,roll_quantity);
          if(value < 0){
              var v = roll_quantity-Math.abs(value);
              $(this).val(v < 0? 0:v);
          }
          validate(false);
      });
   
       $(input).on('change', function (e) {
          var total_quantity = $("#total_quantity").val();
          var roll_quantity =  $(this).val();
          var value = validate_with_total_quantity(parseInt(total_quantity),roll_quantity,roll_quantity);
          if(value < 0){
              var v = roll_quantity-Math.abs(value);
              $(this).val(v < 0? 0:v);
          }
          
          validate(false);
      });
   }
   
   function validate_with_total_quantity(total_quantity,input,extract=0){
      var roll_quantity = parseInt(input.replace(/[^0-9]/g,''));  
      var input_total = 0;
      
      $('.roll-row').each(function(index){
          var element =$(this).find('input[type="number"]');
          var value = $(element).val();
          input_total = input_total+parseInt(value);
      }); 
      
      var total_addition = input_total+roll_quantity-extract;
      var remaining = total_quantity-total_addition;
      update_remaining(remaining);
   
      if(total_quantity <= 0 || total_quantity.toString() == 'NaN'){
          $("input[name='rolls']").val('');
          showRollMessage('Please fillup total quantity first.');
          return false;
      }
      else if(input == ''){
          showRollMessage("Can't left it blank.");
          return false;
      }
      else if(!$.isNumeric(roll_quantity)){
          showRollMessage("Non-numeric value not accepted.");
          return false;
      }
      else if(roll_quantity <= 0 ){
          $("input[name='rolls']").val('');
          showRollMessage("Roll quantity can't be "+roll_quantity);
          return false;
      }
      else if(total_addition > total_quantity){
          showRollMessage("Total entered quantity can't "+total_addition + " , cause exceeds Maximum quantity "+total_quantity);
          return (total_quantity-input_total);
      }
      else if(total_addition <= total_quantity){
          hideRollMessage();
          return roll_quantity;
      }
      else if(total_addition == total_quantity){
          hideRollMessage();
          return roll_quantity;
      }
      else if(total_addition != total_quantity){
          hideRollMessage();
          return roll_quantity;
      }
      else if(roll_quantity > total_quantity){
          showRollMessage("Roll quantity "+roll_quantity + " , exceeds Maximum quantity "+total_quantity);
          return (total_quantity-input_total);
      }
      return false;
   }
   
   $('.roll-row').each(function(index){
      var element =$(this).find('input[reference-number="roll_'+(index+1)+'"]');
      individual_input(element);
      var roll_name =$(this).find('input[reference-name="roll_'+(index+1)+'"]');
      roll_name_change(roll_name); 
   });
   
   function roll_name_change(roll_name){
      $(roll_name).on('keyup',function(){
          console.log($(this).val());
          if($(this).val() == ''){
              showRollMessage("Roll ID can't left blank.");
          }else{
              hideRollMessage();
          }
          $(this).attr('custom-name','true');
      });
   }
   
   function showRollMessage(message,id=null){
      if(!id){
          id= "roll_message";
      }
      $("#"+id).html(message);
   }
   
   function hideRollMessage(id=null){
      if(!id){
          id= "roll_message";
      }
      $("#"+id).html('');
   }
   
   function column_add_to_table(roll_quantity){
          $("#roll-table").show(300);
          var number =($("#roll-table .roll-row").length + 1);
          var roll_name = 'Roll '+ number;
          var newRow = $("<tr class='roll-row'>");
          var cols = '';
          cols += '<td style="cursor:grab"><i class="dripicons-view-apps"></i></td>';
          cols += '<td><input reference-name="roll_'+number+'" custom-name="false" type="text" class="form-control" name="roll_name[]" error-text="Enter roll id" value="' + roll_name + '" /></td>';
          cols += '<td><input reference-number="roll_'+number+'" type="number" min="0" class="form-control" name="roll_quantity[]" value="'+roll_quantity+'" /></td>';
          cols += '<td><button type="button" class="rbtnDel btn btn-sm btn-danger">X</button></td>';
   
          $("input[name='rolls']").val('');
          newRow.append(cols);
          $("table.roll-list tbody").append(newRow);
          
          $('.roll-row').each(function(index){
              var element =$(this).find('input[reference-number="roll_'+number+'"]');
              individual_input(element);
              var roll_name =$(this).find('input[reference-name="roll_'+number+'"]');
              roll_name_change(roll_name); 
          });
          validate(false);  
   }
   
   
   $("table#roll-table tbody").on("click", ".rbtnDel", function(event) {
      $(this).closest("tr").remove();
      var input_total = 0;
      
      $('.roll-row').each(function(index){
       var input = $(this).find('input[type="text"]');
       if($(input).attr('custom-name') == 'false'){
           $(input).val('Roll '+(index+1));
          }
          
          var input_number = $(this).find('input[type="number"]');
          input_total= input_total+parseInt($(input_number).val())
      });
      var total = $("#total_quantity").val();
      var remain = total-input_total;
      update_remaining(remain);
      if($('.roll-row').length < 1){
          $("#roll-table").hide(300);
      }
      validate(false);
   });
   
   
   function update_remaining(remain){
      var i = remain < 0 ? 0:remain;
      remaining_ = i;
      if(i != 0 && i.toString() !='NaN'){
          $("#remaining_container").show(300); 
      }else{
          $("#remaining_container").hide(300); 
      }
      $("#remaining_quantity").html(remaining_);
   }
   
   
   
   //submit
   $('#submit-btn').on("click", function (e) {
              e.preventDefault();
              if (!validate()) {
                  $.LoadingOverlay("show");
                      var formData = new FormData();
                      // Append all form inputs to the formData Dropzone will POST
                      var data = $("#fabric-form").serializeArray();
                      $.each(data, function (key, el) {
                          formData.append(el.name, el.value);
                      });
                  
                      $.ajax({
                          type:'POST',
                          url:'{{route('fabrics.store')}}',
                          data: formData,
                          processData: false,
                          contentType: false,
                          success:function(response){
                              $.LoadingOverlay("hide");
                                if(response.success){

swal({
                                             title: 'Fabric created successfully!',
                                             text: 'redirecting to manage fabrics.',
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
                                    })

                                }else{
                                    $("#roll_message").html(response.message);
                                }
                          },error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
                        });
              }
          });
          
   
   
   








      var submit_button_selector = $("#submit-btn");
      init_listeners();
       function init_listeners(){
              
            select_listner('fabric_types');
            hideButton();
           $("select").each(function(){
            
            if($(this).attr('class') == 'swal2-select'){
                return;
            }

               $(this).change(function() {
                  if($(this).attr('verify-ignore') != 'true' ){
                     validate(false);
                     hideError($(this).parent(),this);
                  }
               });
   
                 $(this).select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                placeholder: $(this).attr('error-text'), 
                 });
   
           });

           $("input").each(function(){
              $(this).change(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                 });
                 $(this).keyup(function() {
                    validate(false);
                    hideError($(this).parent(),this);
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


   
   //validation part
   function validate(show = true) {
   
      //remove
      $("input").each(function(){
          $(this).focus(function() {
              if($(this).attr('type')=='file'){
                  hideError(this,'','h');
              }else{
                  hideError($(this).parent().parent());
              }
          });
      });
   
      $("select").each(function(){
          $(this).change(function() {
          hideError($(this).parent(),this);
          });
      });
   
      //errors
      var has_error = false;
      var input_total = 0;
      var total_quantity = $("#total_quantity").val();
      
      $('.roll-row').each(function(index){
          var element =$(this).find('input[type="number"]');
          var value = $(element).val();
          if(value < 0){
            if(show){
                showRollMessage('Some roll quantity is lower than 0 , remove them otherwise add value.');
            }  
              has_error = true;
          }
          if(value == 0){
              if(show){
                  showRollMessage('Some roll quantity is 0 , remove them otherwise add value.');

              }
              has_error = true;
          }
          if( value == ''){
              if(show){
                  showRollMessage('Some roll quantity is empty , remove them otherwise add value.');
            }
              has_error = true;
          }
          input_total = input_total + parseInt(value);
      }); 
   
      if(input_total != total_quantity){
          has_error = true;
          if(show){

              showRollMessage('Total quantity and each roll quantity summation not equal.');
          }
      }
   
      $("input[type='text']").each(function(){
          if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  && $(this).val() == ''){
              has_error = true;
              if(show){

                  showError($(this).parent().parent(),this);        
              }
          }
      });
   
      $("input[type='number']").each(function(){
          if($(this).val() == ''){
              has_error = true;
              if(show){
                  
                  showError($(this).parent().parent(),this);
            }
          }
          if($(this).val() == 0){
              has_error = true;
              if(show){

                  showError($(this).parent().parent(),this);
              }
          }
      });
      
      $('select').each(function() {
          if($(this).val() == ''){
              has_error = true;
              if(show){

                  showError($(this).parent(),this);
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
   function showError(element,main=null){
      if(!main){
          main = element;
      }
      $(element).addClass('has-error');
      var message_box = $(element).find('.validation-msg');
      $(message_box).html($(main).attr('error-text'));
   }
   
   
   
@endsection