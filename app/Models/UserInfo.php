<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserInfo extends Model
{
    // use HasApiTokens, HasFactory;
    use HasFactory, Notifiable;

    protected $table = 'user_info';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'firstname', 'lastname', 'middlename', 
        'birthdate', 'age', 'sex', 'relationship_status', 'phone_number'
    ];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // user_id in UserInfo refers to id in User
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'student_id', 'id');
    }
}
