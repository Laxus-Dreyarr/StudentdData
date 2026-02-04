<?php
// app/Models/StudentProbation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProbation extends Model
{

    protected $table = 'student_probation';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'start_date', 'end_date',
        'reason', 'status', 'credit_limit'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}