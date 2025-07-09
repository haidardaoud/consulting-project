<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class expert extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table='expert';

    protected $fillable = [
        'name',
        'email',
        'password',
        'bank_account',
        'role',
        'image',
        'expert_details',
        'phone',
        'address','seprice'
        ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
