<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectPrerequisite extends Model
{
    use HasFactory;

    protected $table = 'subjectprerequisites';
    
    // Since this is a pivot table with composite key, we need to specify:
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'subject_id', 'prerequisite_id'
    ];

    // Composite primary key
    protected $primaryKey = ['subject_id', 'prerequisite_id'];

    // Relationship: Link to the main subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Relationship: Link to the prerequisite subject
    public function prerequisite()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_id');
    }

    // Method: Check if a student has completed prerequisites
    public static function checkPrerequisites($subjectId, $completedSubjects)
    {
        $prerequisites = self::where('subject_id', $subjectId)
            ->pluck('prerequisite_id')
            ->toArray();

        return empty(array_diff($prerequisites, $completedSubjects));
    }
}