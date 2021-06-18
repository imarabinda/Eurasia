@extends('template.index')
@section('content')
<style>
    body{
        background:white!important;
        font-family: 'Times New Roman', Times, serif!important;
    }
    
    .table td, .table th{
        padding:0.30rem!important;
    }

    .table thead th{
        border-bottom: none!important;
    }
</style>

<style type="text/css" media="print">
    @page 
    {
        size:  auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */
    }

    html
    {
        background-color: #FFFFFF; 
        margin: 0px;  /* this affects the margin on the html before sending to printer */
    }
    </style>
<div class="row d-flex justify-content-center">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body invoice"> 
                <div class="row form-group text-center justify-content-center">
                     
                        <div class="col-md-12 ml-2">
                            <h5><strong>Delivery Challan</strong></h5>
                            <p>Issue for JobWork</p>
                  </div>
                                            </div>
                                            <div class="col-12 float-left">
                                                <h6>No : # 
                                                    <strong>{{$stitching->challan_number}}</strong>
                                                </h6>
                                                <h6 class=" ">Date : {{date('F j, Y',strtotime($stitching->issue_date))}}</h6>

                                                <h6 class="">Jobwork type : {{$stitching->job_work_type}}</h6>
                                            </div>                                            
                                            <div class="clearfix"> </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    
                                                    <div class="col-5 float-left mt-4">
                                                        <strong>Consignor: {{$stitching->consignor_name}}</strong>
                                                        <address>
                                                            @if($stitching->consignor_address)
                                                            <strong>Address:</strong> {{$stitching->consignor_address}}<br>
                                                            @endif
                                                            
                                                            @if($stitching->consignor_gst_no)
                                                            <strong>GST No. :</strong> {{$stitching->consignor_gst_no}}<br>
                                                            @endif    
                                                        </address>
                                                    </div>

                                                    
                                                    <div class="col-2 float-left mt-4">
                                                        
                                                    </div>
                                                    <div class="col-5 float-right mt-4">
                                                        <strong>Consignee: {{$stitching->vendor_name}}</strong>
                                                        <address>
                                                            @if($stitching->vendor_address)
                                                            <strong>Address:</strong> {{$stitching->vendor_address}}<br>
                                                            @endif
                                                            @if($stitching->vendor_gst_no)
                                                            <strong>GST No. :</strong> {{$stitching->vendor_gst_no}}<br>
                                                            @endif    
                                                        </address>
                                                    </div>
                                                </div>
                                            </div><!--end row-->
                                                                    
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table mt-4">
                                                            <thead>
                                                                <tr><th>Sr.</th>
                                                                <th>Item ID</th>
                                                                <th>Title</th>
                                                                <th>Welted Edges Color</th>
                                                                <th>Size</th>
                                                                <th>Color</th>
                                                                <th>Pcs</th>
                                                            </tr></thead>
                                                            <tbody>

                                                   @php
                                                   $total= 0;
                                                   @endphp             
@foreach($stitching->products as $key => $product)
                                                                <tr>
                                                                    <td>{{$key+1}}</td>
                                                                    <td>{{$product->code}}</td>
                                                                    <td>{{$product->name}}</td>
                                                                    
                                                                    <td>{{$product->welted_edges_color_id ? $product->welted_edges_color_name : ' - '}}</td>


                                                                    
                                                                    <td>{{$product->size_height_width}}</td>
                                                                    <td>{{$product->fabric_color_name}}</td>
                                                                    <td>{{$product->pivot->issued_quantity}}</td>
                                                                </tr>

                                                                @php
                                                                $total +=$product->pivot->issued_quantity; 
                                                                
                                                                @endphp
                                                                @endforeach
                                                                <tr>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td><strong>Total</strong></td>
                                                                    <td>{{$total}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div><!--end row-->
                                            <div class="row mt-3" style="border-radius: 0px;">
                                                <div class="col-md-9">
                                                    <p><strong>Remarks:</strong> Being goods are sent for job work not for sale.<br> Same will be returned back after completion of job work. </p>
                                                </div>
                                                
                                            </div><!--end row-->
        
                                            <hr>
                                            
                                            <div class="row d-flex">
                                                <div class="col-lg-12 col-xl-4 ml-auto align-self-start">
                                                    <h5>Signature</h5>
                                                </div>

                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div><!--end row-->
@endsection


@section('script')
(function() {
    window.print();

    var beforePrint = function() {
        console.log('Please select print.');
    };

    var afterPrint = function() {
        window.close();
    };

    if (window.matchMedia) {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                beforePrint();
            } else {
                afterPrint();
            }
        });
    }

    window.onbeforeprint = beforePrint;
    window.onafterprint = afterPrint;

}());
@endsection