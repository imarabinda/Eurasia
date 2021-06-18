<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Eurasia - @php 
        if(!isset($title)){
           $title =  'Dashboard';
        }@endphp {{$title}} </title>
            
        <meta content="Eurasia - {{$title}}" name="description" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        @php 
        $favicon_path = \App\Models\Setting::where('name','favicon')->value('value'); 
        @endphp
        <link rel="shortcut icon" href="@php echo Storage::url($favicon_path); @endphp">



        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
         <!-- DataTables -->
        <link href="@php echo secure_url('assets/plugins/datatables/dataTables.bootstrap4.min.css');@endphp" rel="stylesheet" type="text/css" />
        <link href="@php echo secure_url('assets/plugins/datatables/buttons.bootstrap4.min.css');@endphp" rel="stylesheet" type="text/css" />
        <!-- Responsive datatable examples -->
        <link href="@php echo secure_url('assets/plugins/datatables/responsive.bootstrap4.min.css');@endphp" rel="stylesheet" type="text/css" /> 


        <link href="@php echo secure_url('assets/plugins/timepicker/tempusdominus-bootstrap-4.css');@endphp" rel="stylesheet" />
        <link href="@php echo secure_url('assets/plugins/timepicker/bootstrap-material-datetimepicker.css');@endphp" rel="stylesheet">
        <link href="@php echo secure_url('assets/plugins/colorpicker/asColorPicker.min.css');@endphp" rel="stylesheet" type="text/css" />
        <link href="@php echo secure_url('assets/plugins/select2/select2.min.css');@endphp" rel="stylesheet" type="text/css" />
   
        <link href="@php echo secure_url('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css');@endphp" rel="stylesheet">
        <link href="@php echo secure_url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css');@endphp" rel="stylesheet">
        <link href="@php echo secure_url('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css');@endphp" rel="stylesheet" />
        <link href="@php echo secure_url('assets/plugins/sweet-alert2/sweetalert2.min.css');@endphp" rel="stylesheet" type="text/css">  




        <link rel="stylesheet" href="@php echo secure_url('assets/plugins/metro/MetroJs.min.css'); @endphp">
        <link rel="stylesheet" href="@php echo secure_url('assets/plugins/morris/morris.css'); @endphp">
        <link href="@php echo secure_url('assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css'); @endphp" rel="stylesheet">

        <link href="@php echo secure_url('assets/css/bootstrap.min.css'); @endphp" rel="stylesheet" type="text/css">
        
        
        <link href="@php echo secure_url('assets/plugins/animate/animate.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo secure_url('assets/css/icons.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo secure_url('assets/css/style.css'); @endphp" rel="stylesheet" type="text/css">
        
        <link href="@php echo secure_url('assets/css/image-uploader.css'); @endphp" rel="stylesheet" type="text/css">
        
        <link href="@php echo secure_url('assets/css/lc_lightbox.css'); @endphp" rel="stylesheet" type="text/css">
        
        <link href="@php echo secure_url('assets/skins/minimal.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo secure_url('assets/skins/light.css'); @endphp" rel="stylesheet" type="text/css">
        <link href="@php echo secure_url('assets/skins/dark.css'); @endphp" rel="stylesheet" type="text/css">



        @if(strpos(Route::currentRouteName(),'index') == false)
        <style>
            body{
                background-color:#f2f2f2;
            }
        </style>

        @endif
    </head>


    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            @if(!isset($except_nav))
            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left">
                    <i class="ion-close"></i>
                </button>

                <!-- LOGO -->
                <div class="topbar-left">
                    <div class="text-center">
                        <!--<a href="index.html" class="logo"><i class="fa fa-paw"></i> Aplomb</a>-->
                        <a href="{{route('dashboard.index')}}" class="logo"><h1 style="margin:0px!important;padding-bottom:4%!important">Eurasia</a>
                    </div>
                </div>
               

                <div class="sidebar-inner " style="overflow-x: hidden;
                overflow-y: auto;" id="sidebar-main">

                    <div id="sidebar-menu">
                        <ul>

                    @php 
                        $items = array(
                                'dashboard.index'=>array(
                                    'icon'=>'dripicons-graph-pie',  
                                    'title'=>'Dashboard',
                                ),
                                'products'=>array(
                                    'icon'=>'dripicons-archive',
                                    'items'=>array(
                                    'products.create'=>
                                    array(
                                        'title'=>'Add Product',
                                        'permission'=>'product-create'),
                                        'products.index'=>array(
                                            'title'=>'Manage Products',
                                            'permission'=>'product-manage'),
                                    ),
                                    'title'=>'Products',
                                ),

                                'fabrics'=>array(
                                    'icon'=>'dripicons-jewel',
                                    'items'=>array(
                                    'fabrics.create'=>
                                    array(
                                        'title'=>'Add Fabric',
                                        'permission'=>'fabric-create',
                                    ),
                                    'fabrics.index'=>
                                    array(
                                        'title'=>'Manage Fabrics',
                                        'permission'=>'fabric-manage',
                                    ),
                                    'cut_pieces.index'=>
                                    array(
                                        'title'=>'Cut Pieces',
                                        'permission'=>'cut-piece-manage',
                                    ),
                                    ),
                                    'title'=>'Raw Materials',
                                ),
                            'production'=>array(
                                'title'=>'Production',
                                'icon'=>'dripicons-view-list',
                                'items'=>array(
                                    'productions.create'=>array('title'=>'Issue Embroidery','permission'=>'production-create'),
                                    'productions.index'=>array('title'=>'Manage Embroideries','permission'=>'production-manage'),
                                'productions.stock'=>array('title'=>'Embroidery Stock','permission'=>'embroidery-stock-manage')
                                )
                            ),

                            'tailor'=>array(
                                'title'=>'Tailor',
                                'icon'=>'dripicons-view-list',
                                
                                'items'=>array(
                                    'tailors.index'=>array('title'=>'Manage Tailors','permission'=>'tailor-manage'),
                                )
                        ),
                        
                        'stiching'=>array(
                                'title'=>'Stitching',
                                'icon'=>'dripicons-view-list',
                                
                                'items'=>array(
                                    'stitches.create'=>array('title'=>'Issue Stitching','permission'=>'stitching-create'),
                                    'stitches.index'=>array('title'=>'Manage Stitches','permission'=>'stitching-manage'),
                                'stitches.stock'=>array('title'=>'Final Stock','permission'=>'final-stock-manage')
                                )
                        ),

                            'shipment'=>array(
                                'title'=>'Shipment',
                                'icon'=>'dripicons-view-list',
                                
                                'items'=>array(
                                    'shipments.create'=>array('title'=>'Issue Shipment','permission'=>'shipment-create'),
                                    'shipments.index'=>array('title'=>'Manage Shipments','permission'=>'shipment-manage'),
                                )
                        ),

                        
                           'rate'=>array(
                                'title'=>'Rate',
                                'icon'=>'dripicons-view-list',
                                
                                'items'=>array(
                                    'rates.index'=>array('title'=>'Manage Rates','permission'=>'rate-manage'),
                                )
                        ),
                        
                            'user'=>array(
                                'title'=>'User',
                                'icon'=>'dripicons-view-list',
                                'items'=>array(
                                    'users.create'=>array('title'=>'Create New User','permission'=>'user-create'),
                                    'users.index' => array('title'=>'Manage Users','permission'=>'user-manage'),
                                )
                            ),

                            
                            'permission'=>array(
                                'title'=>'Permission',
                                'icon'=>'dripicons-view-list',
                                'items'=>array(
                                    'permissions.index'=>array('title'=>'Manage Permissions','permission'=>'permission-manage'),
                                )
                            ),
                            
                            'role'=>array(
                                'title'=>'Role',
                                'icon'=>'dripicons-view-list',
                                'items'=>array(
                                    'roles.create'=> array('title'=>'Create New Role','permission'=>'role-create'),
                                    'roles.index'=> array('title'=>'Manage Roles','permission'=>'role-manage'),
                                )
                            ),

                            'setting'=>array(
                                'title'=>'Settings',
                                'icon'=>'dripicons-view-list',
                                'items'=>array(
                                    'settings.index'=> array('title'=>'Settings','permission'=>'settings-edit'),
                                )
                            ),

                        );
                                
                        
               
                    foreach($items as $key =>$item){

                        $output = '';
                        
                        if(!array_key_exists('items',$item)){
                            $output .= '<li>
                                <a href="'.route($key).'" class="waves-effect waves-light"><span> '.$item['title'].' </span></a>
                            </li>';
                        }else{
                            foreach($item['items'] as $sub_key => $sub){
                            if(auth()->user()->can($sub['permission'])){
                                if(!is_numeric($sub_key)){
                                $url = route($sub_key);
                                }
                                else{
                                $url = '#';
                                }
                            $output .= '<li>
                                <a href="'.$url.'" class="waves-effect waves-light"><span> '.$sub['title'].' </span></a>
                            </li>';
                            }
                            }
                        }
                        if(!empty($output)){
                            echo '<li class="menu-title">'.$item['title'].'</li>'.$output;
                        }
                    }
                @endphp
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div> <!-- end sidebarinner -->
            </div>
            <!-- Left Sidebar End -->



            <!-- Start right Content here -->

            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    <!-- Top Bar Start -->
                    <div class="topbar">
                        <nav class="navbar-custom">
                            <ul class="list-inline float-right mb-0">
                                

                                <style>
                                    #profileImage {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #512DA8;
  font-size: 16px;
  color: #fff;
  text-align: center;
  line-height: 40px;
  margin: 20px 0;
}
                                </style>
                                <li class="list-inline-item dropdown notification-list">
                                    <a id="profileImage" class="nav-link dropdown-toggle arrow-none waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                                        aria-haspopup="false" aria-expanded="false">
                                        {{Auth::user()->first_name[0]}}{{Auth::user()->last_name ? Auth::user()->last_name[0] : ''}}
                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <!-- item-->
                                        <div class="dropdown-item noti-title">
                                            <h5>Welcome {{Auth::user()->first_name}} {{Auth::user()->last_name}}</h5>
                                        </div>
                                        <a class="dropdown-item" href="{{route('profile.edit')}}"><i class="mdi mdi-account-circle "></i> Profile</a>
                                        <div class="dropdown-divider"></div>

                                            <a class="dropdown-item text-danger"  href="#"
                                            onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();"><i class="mdi mdi-power text-danger"></i>
                                                Logout
                                            </a>

                                            <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                    </div>


                                </li>
                            </ul>

                            
                            <div class="clearfix"></div>
                        </nav>
                    </div>
                    <!-- Top Bar End -->
                    @endif

                    <div class="page-content-wrapper">

                        <div class="container-fluid">
