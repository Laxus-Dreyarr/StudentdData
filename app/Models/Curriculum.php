<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculum';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'curriculum_year', 'is_active'
    ];

    // Relationship: Curriculum has many Subjects
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'curriculum_id');
    }

    // Scope: Get active curriculum
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // Scope: Get curriculum by year
    public function scopeByYear($query, $year)
    {
        return $query->where('curriculum_year', $year);
    }
}