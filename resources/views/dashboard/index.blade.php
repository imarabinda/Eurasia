@extends('template.index')
@section('content')

@php
 $product = false;
 $production = false;
 $fabric = false;
 $stitching = false;

@endphp
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="page-title-box">
                                        <h4 class="page-title">Dashboard</h4>
                                    </div>
                                </div>
                            </div>
        
                            <div class="row">
                                @can('product-manage')
                                
@php
 $product = true;
@endphp
                                <div class="col-md-12 col-xl-3">
                                    <div class="card mini-stat">
                                        <div class="mini-stat-icon text-right">
                                            <i class="mdi mdi-cube-outline"></i>
                                        </div>
                                        <div class="p-4">
                                            <h6 class="text-uppercase mb-3">Products</h6>
                                            <div class="float-right">
                                                <p class="mb-0"><b>Today added :</b> {{$products_count_today}}</p>
                                            </div>
                                            <h4 class="mb-0">{{$products_count}}</h4>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                @can('fabric-manage')
                                
@php
 $fabric = true;
 @endphp
                                <div class="col-md-12 col-xl-3">
                                    <div class="card mini-stat">
                                        <div class="mini-stat-icon text-right">
                                            <i class="mdi mdi-buffer"></i>
                                        </div>
                                        <div class="p-4">
                                            <h6 class="text-uppercase mb-3">Fabrics</h6>
                                            <div class="float-right">
                                                <p class="mb-0"><b>Today added:</b> {{$fabrics_count_today}}</p>
                                            </div>
                                            <h4 class="mb-0">{{$fabrics_count}}</h4>
                                        </div>
                                    </div>
                                </div>
                                @endcan
@can('production-manage')
                               
@php
 $production = true;
@endphp
 
                                
                                <div class="col-md-12 col-xl-3">
                                    <div class="card mini-stat">
                                        <div class="mini-stat-icon text-right">
                                            <i class="mdi mdi-settings"></i>
                                        </div>
                                        <div class="p-4">
                                            <h6 class="text-uppercase mb-3">Productions</h6>
                                            <div class="float-right">
                                                <p class="mb-0"><b>Today added:</b> {{$productions_count_today}}</p>
                                            </div>
                                            <h4 class="mb-0">{{$productions_count}}</h4>
                                        </div>
                                    </div>
                                </div>
@endcan
                                
@can('stitching-manage')
                               
@php
 $stitching = true;

@endphp
 
                                
                                <div class="col-md-12 col-xl-3">
                                    <div class="card mini-stat">
                                        <div class="mini-stat-icon text-right">
                                            <i class="mdi mdi-ribbon"></i>
                                        </div>
                                        <div class="p-4">
                                            <h6 class="text-uppercase mb-3">Stitches</h6>
                                            <div class="float-right">
                                                <p class="mb-0"><b>Today added:</b> {{$stitches_count_today}}</p>
                                            </div>
                                            <h4 class="mb-0">{{$stitches_count}}</h4>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                
                            </div><!-- end row -->
                            
                      
                            

            @if($products_count > 0 && $product)
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
            
                                            <h4 class="mt-0 header-title">Products</h4>
                                            <p class="text-muted font-14">Latest 5 added products</p>
            
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="products-table" class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>{{trans('Name')}}</th>
                                                            <th>{{trans('Code')}}</th>
                                                            <th>{{trans('Category')}}</th>
                                                            <th>{{trans('Product Type')}}</th>
                                                            <th>{{trans('Fabric Type')}}</th>
                                                            <th>{{trans('Fabric Color')}}</th>
                                                            <th>{{trans('Size')}}</th>
                                                            <th></th>
                                                        
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($products_count > 0 )
                                                        @php
                                                            $products = $products->take(5)->get();   
                                                        @endphp
                                
                                                            @foreach($products as $product)
                                                                <tr>
                                                                    <td><a href="{{route('products.show',$product->id)}}" target="_blank">{{$product->name}}</a></td>
                                                                    <td><a href="{{route('products.show',$product->id)}}" target="_blank">{{$product->code}}</a></td>
                                                                    <td>{{$product->product_category_name}}</td>
                                                                    
                                                                    @if($product->product_type)
                                                                    <td>{{$product->product_type_name}}</td>
                                                                    @else
                                                                    <td>No</td>
                                                                    @endif

                                                                    @if($product->fabric_type)
                                                                    <td>{{$product->fabric_type_name}}</td>
                                                                    @else
                                                                    <td>No</td>
                                                                    @endif

                                                                    @if($product->fabric_color)
                                                                    <td>{{$product->fabric_color_name}}</td>
                                                                    @else
                                                                    <td>No</td>
                                                                    @endif

                                                                    <td>{{$product->size_height_width}}</td>
                                                                    <td><a href="{{route('products.show',$product->id)}}" class="btn btn-info">View</a></td>
                                                                </tr>
                                                            @endforeach
                                                    @else
                                                        <tr class="odd">
                                                            <td valign="top" colspan="9" >No products added recently.</td>
                                                            </tr>
                                                    @endif
                                                       
                                                        </tbody>
                                                    </table>
                                                </div>
            
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 
            @endif
               



            @if($fabrics_count > 0 && $fabric)
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
            
                                            <h4 class="mt-0 header-title">Fabrics</h4>
                                            <p class="text-muted font-14">Latest 5 added fabrics</p>
            
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="fabrics-table" class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>{{trans('Date')}}</th>
                                                            <th>{{trans('Mill ID')}}</th>
                                                            <th>{{trans('Mill Ref ID')}}</th>
                                                            <th>{{trans('Type')}}</th>
                                                            <th>{{trans('Color')}}</th>
                                                            <th>{{trans('Width')}}</th>
                                                            <th>{{trans('Quantity')}}</th>
                                                            <th>{{trans('Remaining Quantity')}}</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($fabrics_count > 0 )
                                                        @php
                                                         $fabrics = $fabrics->withSum('quantity_used','quantity')->take(5)->get();   
                                
                                                        @endphp
                                                            @foreach($fabrics as $fabric)
                                                                <tr>
                                                                    <td>{{date('F j, Y',strtotime($fabric->receiving_date))}}</td>
                                                                    <td><a href="{{route('fabrics.show',$fabric->id)}}" target="_blank">{{$fabric->mill_id}}</a></td>
                                                                    <td>{{$fabric->mill_ref_id}}</td>
                                                                    <td>{{$fabric->fabric_type->name}}</td>
                                                                    <td>{{$fabric->fabric_color->name}}</td>
                                                                    <td>{{$fabric->width}}</td>
                                                                    <td>{{$fabric->total_quantity}}</td>
                                                                    <td>{{$fabric->total_quantity-$fabric->quantity_used_sum_quantity}}</td>
                                                                    <td><a href="{{route('fabrics.show',$fabric->id)}}" class="btn btn-info">View</a></td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr class="odd">
                                                                <td valign="top" colspan="9" >No fabrics added recently.</td>
                                                                </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
            
                                            </div>


                                        </div>

                                            


                                            
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 


            @endif


            @if($productions_count > 0 && $production )
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
            
                                            <h4 class="mt-0 header-title">Productions</h4>
                                            <p class="text-muted font-14">Latest 5 added productions</p>
            
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="productions-table" class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>{{trans('Issue Date')}}</th>
                                                            <th>{{trans('Vendor Name')}}</th>
                                                            <th>{{trans('Jobwork Type')}}</th>
                                                            <th>{{trans('Products Count')}}</th>
                                                            <th>{{trans('Received Product')}}</th>
                                                            <th></th>
                                                        
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($productions_count > 0 )
                                                        @php
                                                        $productions = $productions->withCount('products')->take(5)->get();   
                                                        @endphp
                                                            @foreach($productions as $production)
                                                                <tr>
                                                                    <td>{{ date('F j, Y',strtotime($production->issue_date))}}</td>
                                                                    <td><a href="{{route('productions.show',$production->id)}}" target="_blank">{{$production->vendor_name}}</a></td>
                                                                    <td>{{$production->job_work_type}}</td>
                                                                    <td>{{$production->products_count}}</td>
                                                                    
                                                                    @php
                                                                     $products=$production->products;
                
                $count = array();
                
                foreach($products as $i =>$product){
                    $issued = $product->pivot->issue_embroidery;
                    $get = \App\Models\EmbroideryStockLog::where([['production_id',$production->id],['product_id',$product->id]])->sum(DB::raw('received_embroidery + received_damage'));
                    if($issued == $get){
                        $count[]=$get;
                    }
                }
   
                                                                    @endphp
                                                                    
                                                                    <td>{{count($count)}}</td>

                                                                    <td><a href="{{route('productions.show',$production->id)}}" class="btn btn-info">View</a></td>
                                                                </tr>
                                                            @endforeach
                                                    @else
                                                        <tr class="odd">
                                                            <td valign="top" colspan="9" >No productions added recently.</td>
                                                            </tr>
                                                    @endif
                                                       
                                                        </tbody>
                                                    </table>
                                                </div>
            
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 
            @endif



            
            @if($stitches_count > 0 && $stitching)
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
            
                                            <h4 class="mt-0 header-title">Stitches</h4>
                                            <p class="text-muted font-14">Latest 5 added stitches</p>
            
                                            <div class="table-rep-plugin">
                                                <div class="table-responsive mb-0" data-pattern="priority-columns">
                                                    <table id="stitches-table" class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>{{trans('Issue Date')}}</th>
                                                            <th>{{trans('Vendor Name')}}</th>
                                                            <th>{{trans('Jobwork Type')}}</th>
                                                            <th>{{trans('Products Count')}}</th>
                                                            <th>{{trans('Received Product')}}</th>
                                                            <th></th>
                                                        
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if($stitches_count > 0 )
                                                        @php
                                                        $stitches = $stitches->withCount('products')->take(5)->get();
                                                        
                                                        @endphp
                                                            @foreach($stitches as $stitching)
                                                                <tr>
                                                                    <td>{{ date('F j, Y',strtotime($stitching->issue_date))}}</td>
                                                                    <td><a href="{{route('stitches.show',$stitching->id)}}" target="_blank">{{$stitching->vendor_name}}</a></td>
                                                                    <td>{{$stitching->job_work_type}}</td>
                                                                    <td>{{$stitching->products_count}}</td>
                                                                    
                                                                    @php
                                                                     $products=$stitching->products;
                
                $count = array();
                
                foreach($products as $i =>$product){
                    $issued = $product->pivot->issue_embroidery;
                    $get = \App\Models\FinalStockLog::where([['stitching_id',$stitching->id],['product_id',$product->id]])->sum(DB::raw('received_stitches + received_damage'));
                    if($issued == $get){
                        $count[]=$get;
                    }
                }
   
                                                                    @endphp
                                                                    
                                                                    <td>{{count($count)}}</td>

                                                                    <td><a href="{{route('productions.show',$production->id)}}" class="btn btn-info">View</a></td>
                                                                </tr>
                                                            @endforeach
                                                    @else
                                                        <tr class="odd">
                                                            <td valign="top" colspan="9" >No stitches added recently.</td>
                                                            </tr>
                                                    @endif
                                                       
                                                        </tbody>
                                                    </table>
                                                </div>
            
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row --> 
            @endif

            @if(!$product || !$fabric || !$production || $stitching)
            
            
            @endif
            
@endsection


@section('script')

        $('#products-table').DataTable(

        {
            "paging":   false,
            "searching": false,
        "info":     false,
        }
        );  

        $('#fabrics-table').DataTable(
        {
            "paging":   false,
            "searching": false,
        "info":     false,
        });  
        $('#productions-table').DataTable(
        {
            "paging":   false,
            "searching": false,
        "info":     false,
        });  
        $('#stitches-table').DataTable(
        {
            "paging":   false,
            "searching": false,
        "info":     false,
        });  

@endsection