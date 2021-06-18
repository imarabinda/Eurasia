<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(){
        $title = 'Manage Settings';
        $settings = \App\Models\Setting::all();
        return view('settings.index',compact('title','settings'));
    }

    
    public function save(Request $request){
        $request->validate([
            'default_rate'=>'required',
        ]);
    
        $data = $request->all();
        
        $data['default_rate']= round($data['default_rate'],2);

        \App\Models\Setting::chunkById(200,function($settings) use ($data){
            foreach($settings as $setting){
                if(!array_key_exists($setting->name,$data)){
                    continue;
                }
                $setting->value = $data[$setting->name];
                $setting->save();

            }
        });

        return response()->json([
            'success'=>true,
            'title'=>'Settings saved.',
            'subtitle'=>'',
            'redirect'=>false
        ]);

    }
    
    
}
