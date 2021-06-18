<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tailor;

class TailorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Manage Tailors';
        return view('tailors.index',compact('title'));
    }


    
    public function list(){

        $auth_user = auth()->user();
        if(!$auth_user->can('tailor-manage')){
            abort(403);
        }
        $platform = request()->platform;

        $model = Tailor::where('id','>',0);
        
        return datatables()
        ->eloquent($model)
        ->addColumn('options',function(Tailor $tailor) use ($auth_user){
            
             $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


                if($auth_user->can('tailor-view')){
                    $action .='<a class="dropdown-item view-tailor" href="'.route('tailors.show',$tailor->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
                }

                if($auth_user->can('tailor-edit')){
                    $action .='<a class="dropdown-item edit-tailor" href="'.route('tailors.edit',$tailor->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
                }
                
                if($auth_user->can('tailor-delete')){
                $action .=
                
                                    \Form::open(["route" => ["tailors.destroy", $tailor->id], "method" => "DELETE"] ).'
                                          <button type="submit" class="dropdown-item btn btn-link delete-button" ><i class="fa fa-trash"></i> '.trans("Delete").'</button> 
                                        '.\Form::close();
                }

                $action .=     '</div>
                                            </div>';
                return $action;

        })
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('id', $order);
        })
        ->rawColumns(['options'])
            
        ->toJson();           
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('tailor-create') || !request()->ajax()){
            abort(403);
        }
        
        return view('tailors.create');
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:255',
            'rate_with_welted_edges'=>'required',
            'rate_without_welted_edges'=>'required',
        ]);

        $data = $request->all();
        
        $data['rate_with_welted_edges'] = round($data['rate_with_welted_edges'],2);
        $data['rate_without_welted_edges'] = round($data['rate_without_welted_edges'],2);
        
        Tailor::firstOrCreate($data);
        
        return response()->json([
            'success'=>true,
            'title'=> 'Tailor added successfully.',
            'subtitle'=>'',
            'redirect'=>false
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tailor $tailor)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('tailor-view') || !request()->ajax()){
            abort(403);
        }
        return view('tailors.show',compact('tailor'));
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tailor $tailor)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('tailor-edit') || !request()->ajax()){
            abort(403);
        }
        return view('tailors.edit',compact('tailor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Tailor $tailor)
    {
        $request->validate([
            'name'=>'required|max:255',
            'rate_with_welted_edges'=>'required',
            'rate_without_welted_edges'=>'required',
        ]);

        $data = $request->all();
        
        $data['rate_with_welted_edges'] = round($data['rate_with_welted_edges'],2);
        $data['rate_without_welted_edges'] = round($data['rate_without_welted_edges'],2);

        $tailor->update($data);

        return response()->json([
            'success'=>true,
            'title'=> 'Tailor updated successfully.',
            'subtitle'=>'',
            'redirect'=>false
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tailor $tailor)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('tailor-delete')){
            abort(403);
        }
        $tailor->delete();
        return redirect('tailors')->with('message', 'Tailor deleted successfully');

    }
}
