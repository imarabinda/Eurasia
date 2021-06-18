@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$shipment->company_name}} || {{$shipment->shipment_id}}</h4>
         @can('shipment-edit')
         <a href="{{route('shipments.edit',$shipment->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit Shipment</a>
         @endcan
      </div>
   </div>
</div>

<form id="product-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body ">
               
                <div class="row form-group">
                  

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Shipment Date</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$shipment->shipment_date}}">
                           <div class="input-group-append calender-open bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Shipment ID</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$shipment->shipment_id}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>
                  

                  <div class="col-md-5 ">
                     <div class="form-group">
                        <label>Company Name*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$shipment->company_name}}">
                        </div>
                        <!-- input-group -->
                        <span class="validation-msg" ></span>
                     </div>
                  </div>

                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Note</label>
                        <div class="input-group">
                           <textarea disabled class="form-control" value="{{$shipment->note}}"></textarea>
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




      <div class="row">
      <div class="col-lg-12" style="margin-bottom:70px">
         
      <h4 class="page-title font-16">Products: </h4>
      <div class="table-rep-plugin">
         <div class="table-responsive mb-0" data-pattern="priority-columns">
            <p class="italic" style="color:red" id="product_message"></p>
            <table id="products-table" class="table table-bordered">    
            </table>
         </div>
      </div>
      
      </div>
   </div>



</form>
@endsection

@section('script')

       var productsTable = $("#products-table").DataTable(  {
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
            url:"{{route('shipments.products')}}",
            data:{
                id:{{$shipment->id}} 
            },
            dataType: "json",
            // type:"post"
        },
        "columns": [
            {"title":"Title","data": "product_name","name":"product.name"},
            {"title":"Item ID","data": "product_code","name":"product.code"},
            {"title":"Category","data": "product.product_category.name"},
            {"title":"Fabric Type","data": "product.fabric_type.name"},
            {"title":"Fabric Color","data": "product.fabric_color.name"},
            {"title":"Size","data": "size","name":"size"},
            {"title":"Issued Shipment Quantity","data": "issued_quantity"},
            ], "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ], 
            });   
         
@endsection