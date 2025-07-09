<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exp_con extends Model
{ protected $table='expert-con';
    protected $fillable=['expert_id','consult_id'];
    use HasFactory;
}
