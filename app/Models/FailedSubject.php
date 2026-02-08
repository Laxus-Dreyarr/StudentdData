<?php
// app/Models/FailedSubject.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedSubject extends Model
{
    protected $table = 'failed_subjects';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'subject_id', 'how_many'
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