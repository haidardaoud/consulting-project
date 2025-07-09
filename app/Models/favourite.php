<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favourite extends Model
{  protected $table='favourite';
    protected $fillable=['user_id','expert_id'];
    use HasFactory;
}
