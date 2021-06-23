<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

use Illuminate\Database\Eloquent\Builder;
class FileController extends Controller
{

    protected $path = 'app/';

    public function files($files,$model, $function = 'files'){
        
        if(!is_array($files)){
            $files = array($files);
        }
            
        $existing_files = $model->$function()->pluck('file_type','id');

        foreach($files as $dir => $file_list){
            
                if(is_numeric($dir)){
                    $dir = 'files';
                }
            
                $params = array(
                    'dir'=>$dir,
                    'old_ids'=>array(),
                    'condition'=>array(),
                    'original_name'=>false,
                );
                
                //conditions
                if(Arr::exists($file_list,$dir.'_condition')){
                    $params['condition'] = explode(',',$file_list[$dir.'_condition']);
                    $raw = ' `file_type` = \''.implode('\' OR `file_type` = \'',$params['condition']).'\'';
                    $existing_files = $model->$function()->where(function(Builder $query) use ($raw){
                    return $query->whereRaw($raw);
        })->pluck('file_type','id');
                    $file_list = Arr::except($file_list,[$dir.'_condition']);   
                }
                if(!is_array($file_list)){
                    $file_list = array($file_list);
                }
                
                //dir
                if(Arr::exists($file_list,$dir.'_dir')){
                    $params['dir'] = $file_list[$dir.'_dir'];
                    $file_list = Arr::except($file_list,[$dir.'_dir']);   
                }
                
                //original name
                if(Arr::exists($file_list,'original_name')){
                    $params['original_name'] = $file_list['original_name'];
                    $file_list = Arr::except($file_list,['original_name']);   
                }

                //old ids
                if(Arr::exists($file_list,$dir.'_old')){
                    $params['old_ids'] = is_array($file_list[$dir.'_old']) ? $file_list[$dir.'_old'] : array_flip(array($file_list[$dir.'_old']));   
                    $file_list = Arr::except($file_list,[$dir.'_old']);   
                }

                $this->store($file_list,$model,$function,$existing_files,$params);
            }
        }
 
        
    private function store($files,$model,$function,$existing_files,$params){
        $file_instances=[];
         if(count($files) > 0 ){
            $create_ids = array_diff_key($files,$existing_files->toArray());
            foreach($create_ids as $file){
                $date_dir = date('Y').'/'.date('m').'/'.ucfirst(str_replace('_files','',$params['dir']));
                    if(array_key_exists('condition',$params)){
                        $file_name = $file->getClientOriginalName();
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        
                        if(array_key_exists(strtolower($ext),array_flip($params['condition']))){
                            $file_instances [] = $this->store_file($params,$file,$date_dir,$ext,$file_name);
                        }else{
                            continue;
                        }
                    }else{
                        $file_instances[] = $this->store_file($file,$date_dir);
                    }
                }
            }

            if(count($file_instances) > 0){
                $model->$function()->saveMany($file_instances);
            }
            $delete_ids = array_diff_key($existing_files->toArray(),$params['old_ids']);
            if(!$model->wasRecentlyCreated && is_array($delete_ids) && count($delete_ids) > 0){
                foreach($delete_ids as $key=>$value){
                    $file = File::find($key);
                    $file->delete();
                 }
            }
            
        }


        private function store_file($params,$file,$date_dir,$ext,$file_name){
            
            if(!$params['original_name']){
                $file_name =  $ext.'-'.date("Y-m-d").rand(132,9999999).uniqid().'.'.$ext;
            }

            $path = Storage::putFileAs(
                    'uploads/'.$date_dir, $file, $file_name
            );

                $file_instance = new File(['link'=>$path,'file_type'=>strtolower($ext)]);
                return $file_instance;
        }

    
    
}


