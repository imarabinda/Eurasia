<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public $guard_names =  ['web','api'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    
    public function index()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('permission-manage')){
            abort(403);
        }
        $platforms = $this->guard_names;
        
        $title = 'Manage Permissions';
        return view('permissions.index',compact('platforms','title'));
    
    }


    //list 

    public function list(){

        $auth_user = auth()->user();
        if(!$auth_user->can('permission-manage')){
            abort(403);
        }
        $platform = request()->platform;

        $model = Permission::when($platform,function ($query, $platform) {
                    return $query->where('guard_name', $platform);
                })->where('name','not like','auth-%')->where('name','not like','roles-auth-%');

        return datatables()
        ->eloquent($model)->editColumn('name',function(Permission $permission){
            return ucwords(str_replace('-',' ',$permission->name));
        })
        ->addColumn('options',function(Permission $permission) use ($auth_user){
            
             $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


                if($auth_user->can('permission-view')){
                    $action .='<a class="dropdown-item view-permission" href="'.route('permissions.show',$permission->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
                }

                if($auth_user->can('permission-edit')){
                    $action .='<a class="dropdown-item edit-permission" href="'.route('permissions.edit',$permission->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
                }
                
                if($auth_user->can('permission-delete')){
                $action .=
                
                                    \Form::open(["route" => ["permissions.destroy", $permission->id], "method" => "DELETE"] ).'
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
        if(!$auth_user->can('permission-create') || !request()->ajax()){
            abort(403);
        }
        
        $platforms = $this->guard_names;
        return view('permissions.create',compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=[];
       

        $data['guard_name'] = $request->permission_platform;


        $reqyest->validate(
            ['permission_name'=>'required|unique:permissions,name',
            'permission_platform'=>'required|exists:permissions,guard_name']
            );
            
        $data['name'] = strtolower(str_replace(' ','-',$request->permission_name));
        
        $permission = Permission::create($data);

        $this->optimize();
        return response()->json([
                'success'=>true
            ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('permission-view') || !request()->ajax()){
            abort(403);
        }
        return view('permissions.show',compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('permission-edit') || !request()->ajax()){
            abort(403);
        }
        $platforms = $this->guard_names;
        
        return view('permissions.edit',compact('permission','platforms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('permission-edit') || !request()->ajax()){
            abort(403);
        }

        $reqyest->validate(
            ['permission_name'=>'required|unique:permissions,name,'.$permission->id,
            'permission_platform'=>'required|exists:permissions,guard_name'
            ]
            );
        $permission->name = strtolower(str_replace(' ','-',$request->permission_name));
    
        $permission->guard_name = $request->permission_platform;
        $permission->save();

        $this->optimize();
        return response()->json([
                'success'=>true
            ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        
        $auth_user = auth()->user();
        if(!$auth_user->can('permission-delete')){
            abort(403);
        }
        $permission->delete();
        $this->optimize();
        return redirect('permissions')->with('message', 'Permission deleted successfully');

    }

    private function optimize(){
        return app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
    
}
