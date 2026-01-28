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


    // Relationship: Subject belongs to a Curriculum
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }

    // Relationship: Subject has many Schedules
    public function schedules()
    {
        return $this->hasMany(SubjectSchedule::class, 'subject_id');
    }

    // Relationship: Subject has many Prerequisites (through pivot table)
    public function prerequisites()
    {
        return $this->belongsToMany(
            Subject::class,
            'subjectprerequisites',
            'subject_id',
            'prerequisite_id'
        );
    }

    // Relationship: Subject is a prerequisite for other subjects
    public function requiredFor()
    {
        return $this->belongsToMany(
            Subject::class,
            'subjectprerequisites',
            'prerequisite_id',
            'subject_id'
        );
    }

    // Relationship: Subject created by a User (assuming User model exists)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
