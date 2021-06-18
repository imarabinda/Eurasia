<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Exports\FabricsExport;
use App\Exports\RollsExport;
use App\Exports\ShipmentsExport;
use App\Exports\CutPiecesExport;
use App\Exports\ProductionsExport;
use App\Exports\EmbroideryStocksExport;
use App\Exports\StitchesExport;
use App\Exports\FinalStocksExport;
use App\Exports\UsersExport;
use App\Exports\TailorsExport;
use App\Exports\RatesExport;

class ExportController extends Controller
{
    public function products(Request $request) 
    {
        $ids = [];
        $file_name = 'Products Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new ProductsExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function fabrics(Request $request) 
    {
        $ids = [];
        $file_name = 'Fabrics Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new FabricsExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    
    public function rolls(Request $request,\App\Models\Fabric $fabric) 
    {
        $ids = [];
        $file_name = $fabric->fabric_type->name.' - '.$fabric->fabric_color->name.' - Rolls Export-'.now();
        
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new RollsExport($ids,$fabric->id,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function cut_pieces(Request $request) 
    {

        $ids = [];
        $file_name = 'Cut Pieces Export-'.now();
        
        $data = $request->all();
        
        return \Excel::download(new CutPiecesExport($data,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function productions(Request $request) 
    {
        $ids = [];
        $file_name = 'Embroideries Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new StitchesExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function embroidery_stocks(Request $request) 
    {
        $ids = [];
        $file_name = 'Embroidery Stock Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new EmbroideryStocksExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function tailors(Request $request) 
    {
        $ids = [];
        $file_name = 'Tailors Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        
        return \Excel::download(new TailorsExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function stitches(Request $request) 
    {
        $ids = [];
        $file_name = 'Stitching Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new StitchesExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function final_stocks(Request $request) 
    {
        $ids = [];
        $file_name = 'Final Stock Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new FinalStocksExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function shipments(Request $request) 
    {
        $ids = [];
        $file_name = 'Shipments Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new ShipmentsExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    } 

    public function rates(Request $request) 
    {
        $ids = [];
        $file_name = 'Rates Export-'.now();
        if($request->has('ids')){
            $ids = array_keys($request->ids);
        }
        return \Excel::download(new RatesExport($ids,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function users(Request $request) 
    {
        $file_name = 'Users Export-'.now();
        $data = $request->all();    
        return \Excel::download(new UsersExport($data,$file_name),$file_name.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
}
