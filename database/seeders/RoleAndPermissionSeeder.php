<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
     

        $permissionsList = array(
            'product'=>['manage','view','create','edit','delete','download-psd','download-emb','download-dst','download-image','download-barcode','download-all'],
            'fabric'=>['manage','view','create','edit','delete'],
            'roll'=>['manage','add-use','view','create','edit','delete','manage-history'],
            'cut-piece'=>['manage'],
            'production'=>['manage','view','create','edit','delete','add-product','remove-product','edit-product','receive','save-challan','print','manage-history'],
            'embroidery-stock'=>['manage'],
            'stitching'=>['manage','view','create','edit','delete','add-product','remove-product','edit-product','receive','save-challan','print','manage-history'],
            'final-stock'=>['manage'],
            'shipment'=>['manage','view','create','edit','delete','add-product','remove-product','edit-product','manage-history'],
            
            'permission'=>['manage','create','view','edit','delete','assign-to-roles'],

            'role'=>['manage','create'],

            'roles-auth'=>['manage','view','edit','delete','assign','view-permission','edit-permission'],
            'settings'=>['edit','db-backup'],  
            'user'=>['manage','create'],
            'auth'=> [
                'manage',
                'view',
                'edit',
                'delete','ban','resend-verification-email','resend-welcome-email','send-password-reset-link','invisible-login'],
            'tailor'=>['manage','edit','create','view','delete'],
            'rate'=>['manage','edit','create','view','delete'],
            
            );
            
            //roles with permissions remove
            $roles = [
                'Super Admin'=>[], // nothing removed all permit
                'Admin'=>[
                    'remove'=>[
                        'permission',
                        'role',
                        'settings',
                        'roles-auth',
                        'auth'=>['invisible-login']
                    ],
                    'add'=>[
                        'roles-auth'=>['assign'] //add individual keys
                    ],
                    'remove_roles'=>['Super Admin','Admin'], //for auth remove roles
                    ],
                    'Creator'=> ['permission','role','settings','user','auth','roles-auth']
            ];
        


        $arrayOfPermissionNames = [];
        $insertRolePermissions = [];

        //create all permission
        foreach($permissionsList as $key => $cruds){
            if(is_array($cruds) && ($key != 'auth' && $key != 'roles-auth')){
                foreach($cruds as $individual){
                    $arrayOfPermissionNames[] = $key.'-'.$individual;
                }
            }
            foreach($roles as $role_name => $not_permit){
                    if($key == 'auth' || $key=='roles-auth'){        
                        foreach($cruds as $individual){
                            $arrayOfPermissionNames[] = $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($role_name));
                        }
                    }
            }

        }
        

        
        //create permissions according to roles
        foreach($roles as $role_name => $rules){
            $tempPermissions = $permissionsList;
            $tempRoles['auth'] = $roles;
            $tempRoles['roles-auth'] = $roles;

            if(is_array($rules)){
                if(count($rules) > 0){
                    if(array_key_exists('remove',$rules)){

                        foreach($rules['remove'] as $remove_key_name => $remove_key){
                            if(is_array($remove_key)){
                                $tempPermissions[$remove_key_name] = array_diff($tempPermissions[$remove_key_name],$remove_key); 
                            }else{
                                unset($tempPermissions[$remove_key]);
                            }
                        }
                        unset($rules['remove']);
                    }

                    if(array_key_exists('remove_roles',$rules)){
                        foreach($rules['remove_roles'] as $role_remove){
                            if(is_array($role_remove)){
                                foreach($role_remove as $role_remove_from){
                                    unset($tempRoles[$role_remove_from][$role_remove]);
                                }
                            }else{
                                unset($tempRoles['auth'][$role_remove]);
                                unset($tempRoles['roles-auth'][$role_remove]);
                            }
                        }
                        unset($rules['remove_roles']);
                    }

                    if(array_key_exists('add',$rules)){
                        foreach($rules['add'] as $add_key_name=>$add_key){
                            if(is_array($add_key)){
                               if(array_key_exists($add_key_name,$tempPermissions)){
                                    $tempPermissions[$add_key_name] = array_merge($tempPermissions[$add_key_name],$add_key);
                                }else{
                                    $tempPermissions[$add_key_name] = $add_key;
                                }
                            }else{
                                $tempPermissions[] = $add_key; 
                            }
                        }
                        unset($rules['add']);
                    }

                    foreach($rules as $remove_key_name => $remove_key){
                        
                        if(is_array($remove_key) && array_key_exists('remove_roles',$remove_key)){
                            foreach($remove_key['remove_roles'] as $role_remove){
                                unset($tempRoles[$remove_key_name][$role_remove]);
                            }
                            unset($remove_key['remove_roles']);   
                        }

                        if(is_array($remove_key) && array_key_exists('remove',$remove_key)){
                            if(is_array($remove_key)){
                                $tempPermissions[$remove_key_name] = array_diff($tempPermissions[$remove_key_name],$remove_key['remove']); 
                            }else{
                                unset($tempPermissions[$remove_key_name]);
                            }
                            unset($remove_key['remove']);
                        }

                        if(is_array($remove_key) && array_key_exists('add',$remove_key)){
                        foreach($remove_key['add'] as $add_key_name=>$add_key){
                            if(is_array($add_key)){
                               if(array_key_exists($add_key_name,$tempPermissions)){
                                    $tempPermissions[$add_key_name] = array_merge($tempPermissions[$add_key_name],$add_key);
                                }else{
                                    $tempPermissions[$add_key_name] = $add_key;
                                }
                            }else{
                                $tempPermissions[] = $add_key; 
                            }
                        }
                            unset($remove_key['add']);
                        }
                            
                        if(is_array($remove_key)){
                            
                            $tempPermissions[$remove_key_name] = array_diff($tempPermissions[$remove_key_name],$remove_key); 
                        }else{
                                unset($tempPermissions[$remove_key_name][$remove_key]);
                        }
                                
                        if(!is_array($remove_key) && is_numeric($remove_key_name)){
                            
                                unset($tempPermissions[$remove_key]);
                        }  
                    }

                }
            }
            
            foreach($tempPermissions as $key => $cruds){
                if(is_array($cruds) && ( $key !='auth' && $key !='roles-auth')){
                    foreach($cruds as $individual){
                        if(is_numeric($key)){
                            $insertRolePermissions[$role_name][] = $individual;
                        }else{
                            $insertRolePermissions[$role_name][] = $key.'-'.$individual;
                        }
                    }
                }else if(!is_array($cruds) && ($key != 'auth' && $key!='roles-auth')){
                    $insertRolePermissions[$role_name][] = $cruds;
                }

                if($key == 'auth' || $key =='roles-auth'){        
                    foreach($tempRoles[$key] as $role_name_new => $permit){   
                        foreach($cruds as $individual){
                            $insertRolePermissions[$role_name][] = $key.'-'.$individual.'-'.str_replace(' ','-',strtolower($role_name_new));
                        }        
                    }
                }
            }
        }

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web','created_at'=>now()];
        });

        Permission::insert($permissions->toArray());

        $initAdminSuperAdmins = [
            'Super Admin'=> ['Arabinda'],
            'Admin'=>['Eurasia']
        ];

        foreach($insertRolePermissions as $role_name=>$permissions){
            $role = Role::firstOrCreate(['name'=>$role_name,'guard_name'=>'web']);
            $role = $role->givePermissionTo($permissions);
            if(array_key_exists($role_name,$initAdminSuperAdmins)){
                foreach($initAdminSuperAdmins[$role_name] as $initAdminSuperAdmin){
                    User::where('name',$initAdminSuperAdmin)->first()->assignRole($role);
                }
            }
        }
    }
}
