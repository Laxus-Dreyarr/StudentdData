<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    // use HasApiTokens, HasFactory;
    use HasFactory, Notifiable;

    protected $table = 'students';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'id_no', 'year_level', 'status', 
        'curriculum', 'is_regular', 'enrolled', 'sy'
    ];

    public function user_information()
    {
        return $this->belongsTo(UserInfo::class, 'student_id', 'id'); //user_id in UserInfo refers to id in User
    }
}
