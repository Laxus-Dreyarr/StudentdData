<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectSchedule extends Model
{
    use HasFactory;

    protected $table = 'subjectschedules';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'subject_id', 'Section', 'Type', 'day', 
        'start_time', 'end_time', 'room'
    ];

    // Cast time fields properly
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationship: Schedule belongs to a Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Scope: Get schedules by day
    public function scopeByDay($query, $day)
    {
        return $query->where('day', $day);
    }

    // Scope: Get schedules for a specific section
    public function scopeBySection($query, $section)
    {
        return $query->where('Section', $section);
    }

    // Scope: Get schedules by type (Lecture/Lab)
    public function scopeByType($query, $type)
    {
        return $query->where('Type', $type);
    }

    // Method: Check if schedule conflicts with another
    public function conflictsWith($otherSchedule)
    {
        return $this->day === $otherSchedule->day && 
               $this->start_time < $otherSchedule->end_time && 
               $this->end_time > $otherSchedule->start_time;
    }
}