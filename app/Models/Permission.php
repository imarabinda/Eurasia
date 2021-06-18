<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    use HasFactory;
    
    // public function getGuardNameAttribute($guard_name){
    //     return strtoupper($guard_name);
    // }

}
