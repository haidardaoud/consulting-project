<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reserve extends Model
{ protected $table='reserve';
    protected $fillable=['last_session','user_id','expert_id','day','hour','end_session'];
    use HasFactory;
}
