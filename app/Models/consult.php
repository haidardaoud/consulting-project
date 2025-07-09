<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class consult extends Model
{ protected $table='consult';
    protected $fillable=['type'];
    use HasFactory;
}
