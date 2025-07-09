<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expert_time extends Model
{ protected $table='expert_time';
    protected $fillable=['expert_id','day','start_time','end_time'];
    use HasFactory;
}
