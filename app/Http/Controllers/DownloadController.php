<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\File;
use ZipArchive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{

    //to determine what will be the directory name
    private $directory_name = array(
        'png'=>'Images',
        'jpeg'=>'Images',
        'jpg'=>'Images',
        'btw'=>'Barcodes',
        'psd'=>'Psds',
        'dst'=>'Dsts',
        'emb'=>'Embs',
        'bin'=>'Files',
    );


    public function index(Request $request){
        $type= $request->type;
        $data = array('id'=>$request->id,'get'=>$request->get);
        switch($type){
            case 'product':
                return $this->multiple_from_product($data);
                break;
            // case 'single_file':
            //     return $this->single_file($data);
            //     break;
            default:
                echo 'Invalid';
                break;
        }
    }


    public function multiple_from_product($data){
        $id = $data['id'];
        $auth_user = auth()->user();
        
        switch($data['get']){
            case 'image':
                if(!$auth_user->can('product-download-image')){
                    abort(403);
                }
                $files = Product::find($id)->images->get();
                return $this->single_or_multiple($files);
                break;
            case 'emb':
                if(!$auth_user->can('product-download-emb')){
                    abort(403);
                }
                $files = Product::find($id)->embs->get();
                return $this->single_or_multiple($files);
                break;
            case 'dst':
                if(!$auth_user->can('product-download-dst')){
                    abort(403);
                }
                $files = Product::find($id)->dsts->get();
                return $this->single_or_multiple($files);
                break;
            case 'psd':
                if(!$auth_user->can('product-download-psd')){
                    abort(403);
                }
                $files = Product::find($id)->psds->get();
                return $this->single_or_multiple($files);
                break;
            case 'barcode':
                if(!$auth_user->can('product-download-barcode')){
                    abort(403);
                }
                $files = Product::find($id)->barcodes->get();
                return $this->single_or_multiple($files);
                break;
            case 'all':
                if(!$auth_user->can('product-download-all')){
                    abort(403);
                }
                $files = Product::where('code',$id)->first()->files;
                return $this->single_or_multiple($files,true);
                break;
            default:
                echo 'invalid';
                break;
        }
    }

    public function single_file($data){
        $file = File::find($data['id']);
        return $this->single_or_multiple($file);
    }


    private function single_or_multiple($files,$ext=false){
        if(count($files) > 1){
            return $this->process_zipping($files,$ext);
        }else{
            return $this->single_file_download($files->first());
        }
    }


    private function single_file_download($file){
        if(!is_null($file) && Storage::exists($file->link)){
            return $this->process_download($file->link);
        }else{
            return abort(500,'File not found.');    
        }
    }



    /****
     * Making zip, add files and then download
     * 
     */
    private function process_zipping($files,$extention_wise = false){
            $zip = new ZipArchive;

            // $out = '/temp';
            // if(!Storage::exists($out)){
            //     Storage::makeDirectory($out);
            // }
            // $fileName = "$out/".rand(0,234567898765).'.zip';

            $fileName = tempnam(sys_get_temp_dir(),rand(033232,234567898765));

            if ($zip->open($fileName, ZipArchive::OVERWRITE) === TRUE)
            {
                foreach ($files as $key => $file) {
                    $file_path = Storage::path($file->link);
                    if($file != 'undefined' && Storage::exists($file->link) ){
                        $arr = explode("/", $file->link);
                        $file_n = end($arr);
                        if($extention_wise){
                            $ext = strtolower(substr($file_n, strpos($file_n, ".") + 1));
                            $dir = array_key_exists($ext,$this->directory_name) ? $this->directory_name[$ext] : 'Files';
                            $zip->addFile($file_path,$dir.'/'.$file_n);
                        }else{
                            $zip->addFile($file_path,$file_n);

                        }
                    }
                }
                $zip->close();
            }
            return $this->process_download($fileName,true);   
    }

    
    private function process_download($file,$temp = false){
        if($temp){

            $file_n= 'zip-'.date("Y-m-d").'-'.uniqid().'.zip';
            header("Content-Type: application/zip");
            header("Content-Length: " . filesize($file));
            header("Content-Disposition: attachment; filename=\"".$file_n."\"");
            readfile($file);
            unlink($file);
            
        }else{
            $arr = explode("/", $file);
            $file_n = end($arr);
            return Storage::download($file,$file_n);
        }
        
    }
    
}
