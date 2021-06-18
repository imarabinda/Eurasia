@extends('template.index')
@section('content')
 



    <div class="row">
       <div class="col-md-12">
        <div class="page-title-box">
            @can('shipment-create')
           <a href="{{route('shipments.create')}}" class="btn btn-info btn-action"><i class="dripicons-plus"></i> {{__('Issue new shipment')}}</a>
            @endcan
        </div>
        </div>
    </div>

                            <div class="row">
                                <div class="col-12">
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="shipment-data-table" class="table table-bordered" style="width: 100%">
                                                        <thead>
                                                            <tr>
                                                            <th>{{trans('Shipment Date')}}</th>
                                                            <th>{{trans('Shipment ID')}}</th>
                                                            <th>{{trans('Company Name')}}</th>
                                                            <th>{{trans('Total Products')}}</th>
                                                            <th class="not-exported">{{trans('Action')}}</th>
                                                        </tr>
                                                        </thead>
                                                        
                                                    </table>
                                                </div>
                                            </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 


@endsection

@section('script')

    



        var productsTable = $('#shipment-data-table').DataTable( {
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
                url:"<?php echo route('shipments.list'); ?>",
                data:{
                    // all_permission: all_permission
                },
                dataType: "json",
                // type:"post"
            },
            "columns": [
               
        {"data": "shipment_date"},
                {"data": "shipment_id"},
                {"data": "company_name"},
                {"data": "products_count"},
                {"data": "options"},
            ],
            'language': {
                'searchPlaceholder': "{{trans('Type Company Name , Shipment date or Shipment ID...')}}",
                'lengthMenu': '_MENU_ {{trans("records per page")}}',
                 "info":      '<small>{{trans("Showing")}} _START_ - _END_ (_TOTAL_)</small>',
                "search":  '{{trans("Search")}}',
                'paginate': {
                        'previous': '<i class="dripicons-chevron-left"></i>',
                        'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
      

    
            "createdRow": function( row, data, dataIndex ) {
                $(row).attr('for', data['id']);
                },

            'columnDefs': [
                {
                    "orderable": false,
                    'targets': [4]
                }, {"className": "dt-center", "targets": "_all"}
            ],
            order:[[4,'desc']],
            dom:'<"row"lfB>rtip',
            'select': { style: 'os', selector: 'td:not(:first-child)'},
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
            
            buttons: [
                 
                {
                    extend: 'excel',
                    text: '{{trans("Excel")}}',
                     action: function(e, dt, node, config ){
                        download('{{route('export.shipments')}}');   
                                            
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

@endsection