<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens,HasRoles,HasFactory;

    protected $fillable = [
    'name',
    'email',
    'password',
    ];
    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
