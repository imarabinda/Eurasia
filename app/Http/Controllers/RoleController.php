<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public $create_permissions = [
        
            'roles-auth'=>['manage','view','edit','delete','assign','view-permission','edit-permission'],
            'auth'=> [
                'manage',
                'view',
                'edit',
                'delete','ban','resend-verification-email','resend-welcome-email','send-password-reset-link','invisible-login'=>'exclude'],
    ];

    public $permissions = array(
            'fabric','roll','cut-piece','production',
            'embroidery-stock','stitching',
            'final-stock','shipment','permission',
            'settings','user','auth',
            'role','roles-auth','product','tailor','rate'
            );
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('role-manage')){
            abort(403);
        }
        $platforms = config('auth.guards');

        $title = 'Manage Roles';
        return view('roles.index',compact('platforms','title'));
    
    }
    public function list()
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('role-manage')){
            abort(403);
        }
        $platform = request()->platform;

        
        $roles = Role::all();
        $permitable_roles= array();
        
        foreach($roles as $role){
            if($auth_user->can('roles-auth-manage-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role->id;
            }
        }

        $model = Role::when($platform,function ($query, $platform) {
                    return $query->where('guard_name', $platform);
                })->whereIn('id',$permitable_roles);

        return datatables()
        ->eloquent($model)
        ->addColumn('options',function(Role $role) use ($auth_user){
            $role_name = str_replace(' ','-',strtolower($role->name));
             $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';


            if($auth_user->can('roles-auth-view-'.$role_name)){
                $action .='<a class="dropdown-item view-permission" href="'.route('roles.show',$role->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
            }

            if($auth_user->can('roles-auth-edit-'.$role_name)){
                $action .='<a class="dropdown-item edit-permission" href="'.route('roles.edit',$role->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
            }
                
                if($auth_user->can('roles-auth-delete-'.$role_name)){
                    $action .=
                
                                    \Form::open(["route" => ["roles.destroy", $role->id], "method" => "DELETE"] ).'
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
        if(!$auth_user->can('role-create')){
            abort(403);
        }
        $platforms = config('auth.guards');

        $permissions = $auth_user->getAllPermissions()->pluck('name','id')->toArray();


        
        $title = 'Create Role';
        $permissionsLists = $this->permissions;
        return view('roles.create',compact('platforms','permissions','permissionsLists','title'));
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('role-create')){
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'guard_name' => 'required|exists:permissions,guard_name',
        ]);


        $data['name'] = ucwords($request->name);
        $data['guard_name'] = $request->guard_name;
        
        $role = Role::firstOrCreate($data);

            if($auth_user->can('permission-assign-to-roles')){
                $current_role_permissions = $auth_user->getAllPermissions()->pluck('name','id')->toArray();

                $arrayOfPermissionNamesCreate=[];
                $arrayOfPermissionNamesGive=[];

                foreach($this->create_permissions as $key => $cruds){
                    foreach($cruds as $item => $individual){
                        $individual = is_numeric($item) ? $individual : $item;
                        
                        $arrayOfPermissionNamesCreate[]= $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($data['name']));
                        
                        if($individual != 'exclude'){
                            $arrayOfPermissionNamesGive[]= $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($data['name']));
                        }
                    }
                }
        
            $permissions_new = collect($arrayOfPermissionNamesCreate)->map(function ($permission)use($data){
                return ['name' => $permission, 'guard_name' => $data['guard_name'],'created_at'=>now()];
            });
            
            Permission::insert($permissions_new->toArray());

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
            $permissions = array_intersect_key($current_role_permissions,$request->permissions);

            $role->givePermissionTo($permissions);

            $auth_user->givePermissionTo($arrayOfPermissionNamesGive);
        }
        return response()->json(
            [
                'title'=>'Role created successfully.',
                'subtitle'=>'',
                'success'=>true,
                'redirect'=>route('roles.show',$role->id),
            ]
        );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $auth_user = auth()->user();
        
        $role_name = str_replace(' ','-',strtolower($role->name));
        if(!$auth_user->can('roles-auth-view-'.$role_name)){
            abort(403);
        }

        $platforms = config('auth.guards');

        $permissions = $role->getAllPermissions()->pluck('name','id')->toArray();
        
        $permissionsLists = $this->permissions;
        $title = 'View Role -'.$role->name;
        
        return view('roles.show',compact('platforms','role_name','permissions','role','permissionsLists','title'));
    }

/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function my_permissions()
    {
        $user = auth()->user();
        
        $permissions = $user->getAllPermissions()->pluck('name','id')->toArray();
        
        $permissionsLists = $this->permissions;
        $title = 'Granted Permissions';
        
        return view('users.permissions',compact('permissions','user','permissionsLists','title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $auth_user = auth()->user();
        $role_name = str_replace(' ','-',strtolower($role->name));
        
        if(!$auth_user->can('roles-auth-edit-'.$role_name)){
            abort(403);
        }

        $platforms = config('auth.guards');

        $role_permissions = $role->getAllPermissions()->pluck('name','id')->toArray();
        $permissions = $auth_user->getAllPermissions()->pluck('name','id')->toArray();

        $permissionsLists = $this->permissions;
        
        $title = 'Edit Role - '.$role->name;
       
        return view('roles.edit',compact('platforms','role_name','permissions','role','role_permissions','permissionsLists','title'));
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $auth_user = auth()->user();
        $role_name = str_replace(' ','-',strtolower($role->name));
        if(!$auth_user->can('roles-auth-edit-'.$role_name)){
            abort(403);
        }    

        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id.'|max:255',
            'guard_name' => 'required|exists:permissions,guard_name',
        ]);

        $data['name'] = ucwords($request->name);
        $data['guard_name'] = $request->guard_name;
        
            
        $current_user_role_permissions = $auth_user->getAllPermissions()->pluck('name','id')->toArray();
        

        if($role->name != $data['name']){

            foreach($this->create_permissions as $key => $cruds){
                foreach($cruds as $item =>$individual){

                    $individual = is_numeric($item) ? $individual : $item;
                    
                    $arrayOfOldPermissionNames[]= $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($role->name));
                }
            }

            $old_name_underscore = str_replace(' ','-',strtolower($role->name));
            $new_name_underscore = str_replace(' ','-',strtolower($data['name']));

    Permission::whereIn('name',$arrayOfOldPermissionNames)->chunkById(100, function ($permissions) use ($old_name_underscore,$new_name_underscore) {
        foreach ($permissions as $permission) {
            $permission->name = str_replace($old_name_underscore,$new_name_underscore,$permission->name);
            $permission->save();
        }
    });

        }
$req_permissions = empty($request->permissions) ? [] : $request->permissions;

        $permissions = array_intersect_key($current_user_role_permissions,$req_permissions);
        
        $permissions = Permission::whereIn('id',array_keys($permissions))->get();
        
    if($auth_user->can('roles-auth-edit-permission-'.$role_name)){
        $role->syncPermissions($permissions);
    }
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role->update($data);
        
        return response()->json(
            [
                'title'=>'Role updated successfully.',
                'subtitle'=>'',
                'success'=>true,
                'redirect'=>false,
                ]
            );
            
            
            
        }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $auth_user = auth()->user();
        if(!$auth_user->can('roles-auth-delete-'.str_replace(' ','-',strtolower($role->name)))){
            abort(403);
        }

            $arrayOfOldPermissionNames=[];
            foreach($this->create_permissions as $key => $cruds){
                foreach($cruds as $individual){
                    $arrayOfOldPermissionNames[]= $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($role->name));
                }
            }
            
        Permission::whereIn('name',$arrayOfOldPermissionNames)->chunkById(100, function ($permissions){
            foreach ($permissions as $permission) {
                $permission->delete();
            }
        });
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role->delete();

        return redirect('roles');
    }


    
    public function seeOwnPermissions(Request $request){
        dd(auth()->user()->getAllPermissions()->pluck('name','id')->toArray());
    }

}
