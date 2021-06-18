<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Fabric;
use App\Models\Stitching;
use App\Models\Production;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $auth_user = auth()->user();
        $start_date = date("Y").'-'.date("m").'-'.'01';
        $end_date = date("Y").'-'.date("m").'-'.date('t', mktime(0, 0, 0, date("m"), 1, date("Y")));

        $products = Product::orderBy('id','desc');
        $products_count = $products->count();
        $products_count_today = Product::where('created_at','>=',date('Y-m-d'))->count();
        

        $productions = Production::orderBy('id','desc');
        $productions_count = $productions->count();
        $productions_count_today = Production::where('created_at','>=',date('Y-m-d'))->count();
        
        
        $fabrics = Fabric::orderBy('id','desc');
        $fabrics_count = $fabrics->count();
        $fabrics_count_today = Fabric::where('created_at','>=',date('Y-m-d'))->count();
        
        $stitches = Stitching::orderBy('id','desc');
        $stitches_count = $stitches->count();
        $stitches_count_today = Stitching::where('created_at','>=',date('Y-m-d'))->count();
        
        return view('dashboard.index',compact('products_count','fabrics_count','products','fabrics','fabrics_count_today','products_count_today','productions_count','productions_count_today','productions','stitches','stitches_count_today','stitches_count'));
    }

    public function filter($start_date,$end_date){
        $data =[];
        $data['product_data'] = Product::where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->count();
        $data['fabric_data'] = Fabric::where('created_at','>=',$start_date)->where('created_at','<=',$end_date)->count();
        
        return $data;
    }
}
