<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tailor extends Model
{
    use HasFactory;
    protected $fillable = ['name','address','gst_no','rate_without_welted_edges','rate_with_welted_edges'];
    
}
