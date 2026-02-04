<?php
// app/Models/IncompleteGrade.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompleteGrade extends Model
{
    protected $table = 'incomplete_grades';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'subject_id', 'grade',
        'date_issued', 'completion_deadline',
        'completion_date', 'final_grade', 'status'
    ];

    protected $casts = [
        'date_issued' => 'date',
        'completion_deadline' => 'date',
        'completion_date' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}