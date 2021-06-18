@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16">Editing Rolls of Fabric >> {{$fabric->fabric_type()->value('name')}} - {{$fabric->fabric_color()->value('name')}}</h4>
         <p class="text-muted font-14" style="font-style:italic"><small>The field labels marked with * are required input fields.</small></p>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-12 mt-2">
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            <table id="rolls-data-table" class="table table-bordered" style="width: 100%">
               
            </table>
         </div>
      </div>
   </div>
   <!-- end col -->
</div>
<!-- end row --> 
@can('roll-add-use')
<div id="roll-modal" tabindex="-1" role="dialog" aria-labelledby="roll" aria-hidden="true" class="modal fade text-left">
   <div role="document" class="modal-dialog">
      <div class="modal-content">

<div class="modal-header">
                                                                <h6 class="modal-title" id="roll_name">Modal title</h6>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true" class="text-dark">×</span>
                                                                </button>
                                                                </div>


         <div class="modal-body">
            <p class="italic"><small>{{trans('The field labels marked with * are required input fields')}}.</small></p>
            <form id="cut-pieces-form">
               <table id="cut-pieces-table">
                  <thead>
                     <tr style="background:white!important;color:black">
                        <th >{{trans('Size')}} *</th>
                        <th >{{trans('Cut Pieces')}} *</th>
                     </tr>
                  </thead>
                  <tbody id="tbody-cutl">
                  </tbody>
               </table>
               <div class="form-group mt-3">
                  <label style="color:black">Used quantity*</label>
                  <div class="input-group" style="width:50%">
                     <input type="number" class="form-control" error-text="Enter used quantity..." placeholder="Add used quantity" min="1" name="used_quantity" id="used_quantity">
                  </div>
                  <!-- input-group -->
                  <span class="validation-msg" ></span>
               </div>
               <p class="italic" style="color:red" id="roll_message"></p>
               <div class="form-group">
                  <button type="submit" class="btn btn-primary update-roll">{{trans('Update')}}</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>   
@endcan
@endsection

@section('script')

var row_data = '<tr><td style="width:30%;vertical-align:top"><select error-text="Select size" name="sizes[]" required class="select2 form-control mb-3 custom-select js-states form-control" style="width: 100%; height:36px;"><option></option>'+<?php foreach($sizes as $id => $size) { 
      ?> 
       '<option value="{{$size->id}}" >{{$size->height}} x {{$size->width}}</option>' + 
   <?php } ?>
     '</select><span class="validation-msg"></span></td>'+
     '<td style="width:45%;vertical-align:top"><input error-text="Enter cut pieces" type="number" name="cut_pieces[]" placeholder="Cut Pieces..." class="form-control" id="cut-pieces" aria-describedby="cut-pieces" required><span class="validation-msg"></span></td><td style="vertical-align:top"><button type="button" style="display:none;font-size:15px!important" class="rbtnDel btn btn-danger">X</button><button type="submit" class="btn btn-primary add-new">+</button></td></tr>';
   
   

   
   
   
       
       $(document).on("click",".add-new",function(e){
           e.preventDefault();
           $(this).hide(300);
           $(this).prev().show(300);
           add_row();
       })
   

       $("table#cut-pieces-table tbody").on("click", ".rbtnDel", function(event) {
        var selectOn = $(this);
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
               
            $(selectOn).closest("tr").remove();
                validate(false);
            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    
                }
            });
        
       });
       
   
       function add_row(){
       $("#cut-pieces-table tbody").append(row_data); 
       
       $("input").each(function(){
           if($(this).attr('name') != 'used_quantity'){
$(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent());
                   }
               });

                $(this).change(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                 });

                 $(this).keyup(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                });
           }
               

           });
                  
        $('select').each(function(){

            if($(this).attr('class') == 'swal2-select'){
                return;
            }

            $(this).select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                placeholder: $(this).attr('error-text'),   
            });

            $(this).change(function(){
                validate(false);
            });
        });
        validate(false);
       }
           
               var rollTable = $('#rolls-data-table').DataTable( {
               responsive: false,
               scrollX:true,
               fixedHeader: {
                   header: true,
                   footer: true
               },
               "processing": true,
               "serverSide": true,
               "serverMethod": 'post',
               "ajax":{
                   url:"<?php echo route('rolls.list'); ?>",
                   data:{
                       id:'<?php echo $rolls; ?>',  
                   },
                   dataType: "json",
                   // type:"post"
               },
               dom:'<"row"lfB>rtip',
                "createdRow": function( row, data, dataIndex ) {            
                   $(row).attr('data-id', data['id']);
                   $(row).attr('data-quantity', Math.abs(data['remaining_quantity']));
                   $(row).attr('data-used', data['quantity_used']);
                   $(row).attr('data-name', data['name']);
               },
        "columns": [
            
@can('roll-manage-history')
              {
            className: 'details-control-history',
            orderable: false,
            data: null,
            defaultContent: '',
            width: '4%',
            "searchable":false,
        },
        @endcan
                   {"title":"Roll ID","data": "name"},
                   {"title":"Issued Quantity","data": "quantity"},
                   {"title":"Quantity used","data": "used_quantity"},
                   {"title":"Remaining Quantity","data": "remaining_quantity"},
                   @can('roll-add-use')
                   {"title":"<i class='dripicons-checkmark'></i>","data": "options"},
                   @endcan
            ],"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
               
               'language': {
                   'searchPlaceholder': "{{trans('Type Roll ID or Quantity ...')}}",
                   'lengthMenu': '_MENU_ {{trans("records per page")}}',
                    "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                    "search":  '{{trans("Search")}}',
                    'paginate': {
                           'previous': '<i class="dripicons-chevron-left"></i>',
                           'next': '<i class="dripicons-chevron-right"></i>'
                   }
               },
               'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],

               

                   buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                        download('{{route('export.rolls',$rolls)}}');   
                                            
                    }
                }
                ,
                
                {
                    extend: 'colvis',
                    text: '{{trans("Column visibility")}}',
                    {{-- columns: ':gt()' --}}
                },
                ],
               
           } );


           @can('roll-add-use')
               $('.update-roll').click(function(e){
                   e.preventDefault();
                   if(!validate()){
                            $.LoadingOverlay("show");
                           var formData = new FormData();
   
                           var data = $("#cut-pieces-form").serializeArray();
                           $.each(data, function (key, el) {
                               formData.append(el.name, el.value);
                           });
                       
                       formData.append('id',update_id);
   
                       $.ajax({
                               type:'POST',
                               url:'{{route('rolls.update')}}',
                               data: formData,
                               processData: false,
                               contentType: false,
                               success:function(response){
                                   $.LoadingOverlay("hide");
                                   if(response.success){
                                    rollTable.draw();
                                    
                                    $('#roll-modal').modal('hide');
                                        swal({
                                             title: 'Successful!',
                                             text: response.message,
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

                                   }else{
                                        showRollMessage(response.message);
                                   }
                               },error:function(error,message,response){
                             var errors  =  error.responseJSON.errors;
                             
                             $.each(errors, function(i, item) {
                                 var element = $('#'+i);
                                 showError($(element).parent().parent(),element,item[0]);
                              });
                          }
                           });
   
           
                   }else{
                       console.log('has-error');
                   }
               });
   
               @endcan
      var submit_button_selector = $(".update-roll");
      init_listeners();
       function init_listeners(){
            
        hideButton();

           $("input").each(function(){
               $(this).focus(function() {
                   if($(this).attr('type')=='file'){
                       hideError(this,'','h');
                   }else{
                       hideError($(this).parent());
                   }
               });

                $(this).change(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                 });

                 $(this).keyup(function() {
                    validate(false);
                    hideError($(this).parent(),this);
                });

           });
   
           $("select").each(function(){
               $(this).change(function() {
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

       function validate(show = true) {
   
           //errors
           var has_error = false;
           
           if($("input[name='used_quantity']").val() > total_quantity){
               has_error = true;
               if(show){
                   showRollMessage("Roll used quantity cannot bigger than available quantity.");
               }
           }
   
   
           $("input[type='text']").each(function(){
               if(!$(this).attr('role') && $(this).attr('verify-ignore') != 'true'  && $(this).val() == ''){
                   has_error = true;
                   if(show){
                       showError($(this).parent(),this);        

                   }
               }
           });
   
           $("input[type='number']").each(function(){
               if($(this).val() == ''){
                   has_error = true;
                   if(show){
                       showError($(this).parent(),this);
                   }
               }
               if($(this).val() == 0){
                   has_error = true;
                   if(show){
                       showError($(this).parent(),this);
                   }
               }
           });
           
           $('select').each(function() {

            if($(this).attr('class') == 'swal2-select'){
                return;
            }


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
    


$("input[name='used_quantity']").on("keyup",function(){
           var input = $(this).val();
          var value = validate_with_total_quantity(total_quantity,input);
          if(value >= 0){
              $(this).val(Math.abs(value));
          }
       });
       
   
   
   
   
       function validate_with_total_quantity(total_quantity,input,extract=0){
           var roll_message = $("#roll_message");
           var roll_quantity = parseInt(input.replace(/[^0-9]/g,''));  
           var total_addition = roll_quantity-extract;
           
           if(total_quantity <= 0){
               $("input[name='rolls']").val('');
               $(roll_message).text('Please fillup total quantity first.');
               return false;
           }else if(input == ''){
               $(roll_message).text("Can't left it blank.");
               return false;
           }else if(!$.isNumeric(roll_quantity)){
               $(roll_message).text("Non-numeric value not accepted.");
               return false;
           }else if(roll_quantity <= 0 ){
               $("input[name='rolls']").val('');
               $(roll_message).text("Used quantity can't be "+roll_quantity);
               return false;
           }else if(total_addition > total_quantity){
               $(roll_message).text("Total entered used quantity can't "+total_addition + " , cause exceeds Maximum quantity "+total_quantity);
               return (total_quantity);
           }else if(total_addition <= total_quantity){
               $(roll_message).text('');
               return roll_quantity;
           }else if(total_addition == total_quantity){
               $(roll_message).text('');
               return roll_quantity;
           }
           else if(total_addition != total_quantity){
               $(roll_message).text('');
               return roll_quantity;
           }
           else if(roll_quantity > total_quantity){
               $(roll_message).text("Used quantity "+roll_quantity + " , exceeds Maximum quantity "+total_quantity);
               return (total_quantity);
           }
           return false;
       }
   
       var update_id = total_quantity = input_total = 0;
   
       $(document).on("click", ".btn-add-roll-use", function(){
               $("#cut-pieces-table tbody").html('');
               $('#roll-modal').modal('show');      
               $("input[name='used_quantity']").val('');
               add_row();
              hideRollMessage();
               update_id = $(this).parent().parent().parent().data('id');
               total_quantity = $(this).parent().parent().parent().data('quantity');
               input_total = $(this).parent().parent().parent().data('used');
               $("#roll_name").html($(this).parent().parent().parent().data('name') + ' - Remaining : '+ $(this).parent().parent().parent().data('quantity'));
           });
           
           
           
   
   
   
   
       //error hide
       function hideError(element,main=null){
           if(!main){
               main = element;
           }
           $(element).removeClass('has-error');
           var message_box = $(element).find('.validation-msg');
           $(message_box).text('');
       }
       //error show
       function showError(element,main=null){
           if(!main){
               main = element;
           }
           $(element).addClass('has-error');
           var message_box = $(element).find('.validation-msg');
           $(message_box).text($(main).attr('error-text'));
       }
   



@can('roll-manage-history')
    var roll_id,quantity_id = null;
            
    $('#rolls-data-table tbody').on('click', 'td.details-control-history', function () {
    var tr = $(this).closest('tr');
    
    roll_id = $(tr).attr('data-id');
        
    var row = rollTable.row( tr );
    if ( row.child.isShown() ) {
        // This row is already open - close it
        destroyChild(row);
        tr.removeClass('shown');
     }
    else {
        // Open this row
        createChild(row,tr);
        tr.addClass('shown');
     }
        } );




    function createChild ( row ,tr, sub = true) {
    // This is the table we'll convert into a DataTable
    var table = $('<table class="display" width="100%"/>');
    // Display it the child row
    
    row.child( table ).show();
    $(tr).next().addClass('no-padding');
     if(sub){
        history_table(table);
    }else{
        quantity_history_table(table);    
    }
           
   }
   
        
        function destroyChild(row) {
            var table = $("table", row.child());
            table.detach();
            table.DataTable().destroy();
            
            // And then hide the row
            row.child.hide();
        }
        
var childTable = {};
        
function history_table(table){            
    // Initialise as a DataTable
    childTable[roll_id] = table.DataTable(  {
        responsive: false,
        scrollX:false,
        "paging":   false,
        "searching": false,
        "info":     false,
        fixedHeader: {
            header: true,
            footer: true
        },
        "processing": true,
        "serverSide": true,
        "serverMethod": 'post',
        "ajax":{
            url:"{{route('rolls.add.history')}}",
            data:{
                roll_id:roll_id,
            },
            dataType: "json",
            // type:"post"
        },
        createdRow: function (row, data, index) {
        $(row).addClass("highlightExpanded");
        $(row).attr("for-quantity",data['id']);
        $(row).attr("for-roll",data['fabric_roll_id']);
    
    },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
        "columns": [
            {
            className: 'details-control-size',
            orderable: false,
            data: null,
            defaultContent: '',
            width: '4%',
            "searchable":false,
        },
        {"title":"Date","data": "created_at"},
            {"title":"Quantity","data": "quantity"},
            ],
            "order": [[ 1, "desc" ]],
            });
            child_add_listner($(table[0]).attr("id"));
    } 
    
    
function quantity_history_table(table){            
    // Initialise as a DataTable
    var quantityHistoryTable = table.DataTable(  {
        responsive: false,
        scrollX:false,
        "paging":   false,
        "searching": false,
        "info":     false,
        fixedHeader: {
            header: true,
            footer: true
        },
        "processing": true,
        "serverSide": true,
        "serverMethod": 'post',
        "ajax":{
            url:"{{route('rolls.quantity.history')}}",
            data:{
                quantity_id:quantity_id,
            },
            dataType: "json",
            // type:"post"
        },
        createdRow: function (row, data, index) {
        $(row).addClass("highlightExpandedSecond");
     },"columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
        "columns": [
            
            {"title":"Date","data": "created_at"},
            {"title":"Size","data": "size"},
            {"title":"Pieces","data": "pieces"},
            ],

            "order": [[ 1, "desc" ]],
            });

            
    }



    
function child_add_listner(table){

    $('#'+table+' tbody').on('click', 'td.details-control-size', function () {
    var tr = $(this).closest('tr');
    
    quantity_id = $(tr).attr('for-quantity');
    roll_id = $(tr).attr('for-roll');
    
    var row = childTable[roll_id].row( tr );

    if ( row.child.isShown() ) {
        // This row is already open - close it
        destroyChild(row);
        tr.removeClass('shown');
    }
    else {
        // Open this row
        createChild(row,tr,false);
        tr.addClass('shown');
    }
        } );

        }

        @endif
@endsection