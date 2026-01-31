<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory;
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'email2', 'password', 'profile', 'date_created', 
        'user_type', 'is_active', 'last_login'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Define the column used for authentication (email)
    public function getEmailForPasswordReset()
    {
        return $this->email2;
    }

    // Define the username field for authentication
    public function username()
    {
        return 'email2';
    }

    // Specify the guard for this model
    protected $guard = 'student';

    // Define relationship with UserInfo model
    public function user_information()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id'); //user_id in UserInfo refers to id in User
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'user_id', 'id');
    }
}
