<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;

use Illuminate\Validation\Rules\Password;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $auth_user = auth()->user();
        if(!$auth_user->can('user-manage')){
            abort(403);
        }
        $roles = Role::all();
        
        $title = 'Manage Users';
        $permitable_roles= array();
        
        foreach($roles as $role){
            if($auth_user->can('auth-manage-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role->name;
            }
        }

        return view('users.index',compact('title','permitable_roles'));

    }

    public function list(){

            
        if(!auth()->user()->can('user-manage')){
            abort(403);
        }
        
        $roles = Role::all();
        $auth_user= auth()->user();
        $permitable_roles= array();
        
        foreach($roles as $role){
            if($auth_user->can('auth-manage-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role->id;
            }
        }

        $role = request()->role;

        $model = User::whereHas('roles', function ($query) use ($permitable_roles,$role) {
                $query->when($role,function ($query, $role) {
                    return $query->where('name', $role);
                })->whereIn('id',$permitable_roles);
            return $query;
        });


        return datatables()->eloquent($model)->addColumn('role',function(User $user){
            $names = $user->roles->pluck('name')->toArray();
            return implode(',',$names);
        })
        ->addColumn('options',function(User $user)use($auth_user){
            $user_role = str_replace(' ','-',strtolower($user->roles->pluck('name')->first())); 
            
             $action ='<div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> 
                                </button>
                                <div class="dropdown-menu" >';

            if($user->id == $auth_user->id){

                $action .= '<a class="dropdown-item" href="'.route('profile.edit').'"><i class="fa fa-eye"></i> '.trans('Edit Profile').'</a>';
                $action .= '</div></div>';
                
                return $action;
            }

            if($auth_user->can('auth-view-'.$user_role)){
                $action .='<a class="dropdown-item" href="'.route('users.show',$user->id).'"><i class="fa fa-eye"></i> '.trans('View').'</a>';
            }
            
            if($auth_user->can('auth-edit-'.$user_role)){
                 $action .='<a class="dropdown-item" href="'.route('users.edit',$user->id).'"><i class="fa fa-edit"></i> '.trans('Edit').'</a>';
            }

            if($auth_user->can('auth-ban-'.$user_role)){
                if($user->is_active==0){
                    $text = 'Un-ban';
                } else{
                    $text = 'Ban';
                }
             $action .='<a class="dropdown-item ban" href="javascript:void(0)" data-href="'.route('users.ban',$user->id).'"><i class="fa fa-edit"></i> '.trans($text).'</a>';   
            }

            if($auth_user->can('auth-delete-'.$user_role)){
                
                $action .= \Form::open(["route" => ["users.destroy", $user->id], "method" => "DELETE"] ).'
                                          <button type="submit" class="dropdown-item btn btn-link delete-button" ><i class="fa fa-trash"></i> '.trans("Delete").'</button> 
                                        '.\Form::close();
            }
                
                                      

                $action .=     '</div>
                                            </div>';
            

                return $action;
            

        })
        
        ->orderColumn('options', function ($query, $order) {
                     $query->orderBy('created_at', $order);
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
        $title = 'Add New User';
        $auth_user= auth()->user();
        
        if(!$auth_user->can('user-create')){
            abort(403);
        }
        
        $roles = \Spatie\Permission\Models\Role::all();

        $permitable_roles= array();

        foreach($roles as $role){
            if($auth_user->can('roles-auth-assign-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role;
            }
        }

        return view('users.create',compact('title','permitable_roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $auth_user= auth()->user();
        
        if(!$auth_user->can('user-create')){
            abort(403);
        }

        $roles = \Spatie\Permission\Models\Role::all();
        $permitable_roles= array();

        foreach($roles as $role){
            if($auth_user->can('roles-auth-assign-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[$role->id] = $role->id;
            }
        }

        $request->validate([
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email:rfc,dns,spoof,filter|unique:users,email',
            'phone'=>'required|integer|unique:users,phone',
            'role'=>'required|in:'.implode(',',$permitable_roles)
        ]);

        $data = $request->except('data');
        $data['is_active'] = 1;
        
        $data['name'] = strtolower(strstr($data['email'], '@', true));

        $user = User::create($data);
        
        
        if(array_key_exists($data['role'],$permitable_roles)){
            $user->assignRole($data['role']);
        }

        $expiresAt = now()->addDay();
        $user->sendEmailVerificationNotification();
        $user->sendWelcomeNotification($expiresAt);
        
        $response['redirect']=route('users.show',$user->id);
        $response['success']=true;
        $response['title']='User Created successfully';
        $response['subtitle']='Redirecting to view user.';
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
    
        $auth_user= auth()->user();
        $user_role = str_replace(' ','-',strtolower($user->roles->pluck('name')->first()));
        
        if(!$auth_user->can('auth-view-'.$user_role)){
            abort(403);

        }

        $title = 'View User - '.$user->first_name.' '.$user->last_name;
        return view('users.show',compact('user','title','user_role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

        $auth_user= auth()->user();
        
        if($user->id == $auth_user->id){
            return redirect('profile');    
        }

        $user_role = str_replace(' ','-',strtolower($user->roles->pluck('name')->first()));
        if(!$auth_user->can('auth-edit-'.$user_role)){
            abort(403);
        }

        
        $roles = \Spatie\Permission\Models\Role::all();

        $permitable_roles= array();

        foreach($roles as $role){
            if($auth_user->can('roles-auth-assign-'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[]=$role;
            }
        }


        $title = 'Edit User - '.$user->first_name.' '.$user->last_name;
        return view('users.edit',compact('user','title','user_role','permitable_roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $user)
    {

        $auth_user= auth()->user();
        
        if($user->id == $auth_user->id){
            return redirect('profile');    
        }
        
        $user_role = $user->roles->pluck('name')->first();
        if(!$auth_user->can('auth-edit-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }

        $roles = \Spatie\Permission\Models\Role::all();
        $permitable_roles= array();
        foreach($roles as $role){
            if($auth_user->can('roles-auth-assign--'.str_replace(' ','-',strtolower($role->name)))){
                $permitable_roles[$role->id]=$role->id;
            }
        }
        
        $request->validate([
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email:rfc,dns,spoof,filter|unique:users,email,'.$user->id,
            'phone'=>'required|integer|unique:users,phone,'.$user->id,
            'role'=>'required|in:'.implode(',',$permitable_roles)
        ]);
        $data = $request->except('data');
        
        // $data['is_active'] = 1;
        
        
        
        if(array_key_exists($data['role'],$permitable_roles)){
            $user->assignRole($data['role']);
        }

        
        if($data['email'] != $user->email){
            $data['email_verified_at'] = null;
            $data['name'] = strstr($data['email'], '@', true);
         }
        
        
        $user->update($data);

        if(is_null($data['email_verified_at'])){
            $user->sendEmailVerificationNotification();
        }
        
        $response['redirect']=route('users.show',$user->id);
        $response['success']=true;
        $response['title']='User Updated successfully';
        $response['subtitle']='Redirecting to view user.';
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $auth_user= auth()->user();
        
        if($user->id == $auth_user->id){
            return redirect('profile');    
        }
        
        $user_role = $user->roles->pluck('name')->first();
        if(!$auth_user->can('auth-delete-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }
        $user->delete();
    
        return redirect('users')->with('message', 'User deleted successfully');
    
        $response['success'] = true;
        $response['title'] = 'User deleted successfully.';
        $response['subtitle'] = '';
        return response()->json($response);

    }

    /**
     * Ban the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ban(User $user)
    {
        $auth_user= auth()->user();
        if($user->id == $auth_user->id){
            return redirect('profile');    
        }
        
        $user_role = $user->roles->pluck('name')->first();
        if(!$auth_user->can('auth-ban-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }
        if($user->is_active == 0){
            $text = 'unbanned';
            $button_text = 'Ban';
            $user->is_active = 1;
        }else{
            $text = 'banned';
            $button_text = 'Un-Ban';
            $user->is_active = 0;
        }
        $user->save();
        $response['success'] = true;
        $response['title'] = 'User '.$text.' successfully.';
        $response['subtitle'] = '';
        $response['text']=ucfirst($button_text);
        return response()->json($response);
    }


     public function resend_email(Request $request, User $user)
    {
        $auth_user= auth()->user();
        
        $user_role = $user->roles->pluck('name')->first();
        if(!$auth_user->can('auth-resend-verification-email-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                        ? new JsonResponse([], 204)
                        : redirect($this->redirectPath());
        }
        $user->sendEmailVerificationNotification();
        
        $response['success']=true;
        $response['title']='Verification email resent successful.';
        $response['subtitle']='';
        return response()->json($response);
    
    }

    public function resend_welcome_email(User $user){

        $auth_user= auth()->user();

        $user_role = $user->roles->pluck('name')->first();
        if($user->password || !$auth_user->can('auth-resend-welcome-email-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }

        $expiresAt = now()->addMinutes(60);
        $user->sendWelcomeNotification($expiresAt);
        
        $response['success']=true;
        $response['title']='Welcome email resent successful.';
        $response['subtitle']='';
        return response()->json($response);
    }


    public function profile(){
        $title = 'Edit Profile';
        return view('users.profile',compact('title'));
    }

    public function profile_update(Request $request){
        $user = Auth::user();
       
        $request->validate(
            [
                'new_password' => 'nullable|max:20|min:8',
                'confirm_password'=>'nullable|required_unless:new_password,null|same:new_password'
            ]
        );
        $message = [];

        if($request->has('new_password')){
            $user->password = \Hash::make($request->new_password);
            $message[] = 'Password updated successfully.';
        }
        if(count($message) > 0){
            $message[0] = 'Profile updated successfully.';
        }

        $user->save();

        return response()->json(
            [
                'success'=>true,
                'title'=>$message[0],
                'subtitle'=>'',
                'redirect'=>route('profile.edit')
                ]
        );
    }

    public function invisible_login(User $user){
        
        $auth_user= auth()->user();
        $user_role = $user->roles->pluck('name')->first();
        if(!$auth_user->can('auth-invisible-login-'.str_replace(' ','-',strtolower($user_role)))){
            abort(403);
        }
        Auth::login($user);
        session()->put('invisible',true);
        return response()->json(
            [
                'success'=>true,
            ]
        );
    }
}
