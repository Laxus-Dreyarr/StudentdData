<?php
// app/Models/StudentWarning.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentWarning extends Model
{
    protected $table = 'student_warnings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'warning_type', 'reason', 
        'issued_date', 'expiry_date', 'status',
        'related_subject_ids'
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'related_subject_ids' => 'array'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}