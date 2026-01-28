<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Subject extends Model
{
     // use HasApiTokens, HasFactory;
    use HasFactory, Notifiable;

    protected $table = 'subjects';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code', 'name', 'description', 'units', 
        'year_level', 'semester', 'max_students', 'curriculum_id', 'created_by', 'date_created', 'is_active'
    ];


}
