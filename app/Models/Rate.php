<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{   protected $table='rate';
    protected $fillable=['expert_id',
   'user_id','num'];
    use HasFactory;
}
