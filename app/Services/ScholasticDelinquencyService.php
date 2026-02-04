<?php
// app/Services/ScholasticDelinquencyService.php
namespace App\Services;

use App\Models\Student;
use App\Models\EnrolledSubjects;
use App\Models\StudentWarning;
use App\Models\StudentProbation;
use App\Models\IncompleteGrade;
use Carbon\Carbon;

class ScholasticDelinquencyService
{
    /**
     * Check and apply scholastic delinquency rules for a student
     */
    public function checkStudentDelinquency($studentId)
    {
        $student = Student::with(['enrolledSubjects' => function($query) {
            $query->where('sy', '2025-2026'); // Current school year
        }])->find($studentId);

        if (!$student) {
            return ['error' => 'Student not found'];
        }

        $results = [
            'warnings_issued' => [],
            'probation_status' => null,
            'incomplete_grades' => []
        ];

        // Check for failing grades and issue warnings
        $this->checkFailingGrades($student, $results);
        
        // Check for incomplete grades
        $this->checkIncompleteGrades($student, $results);
        
        // Check for probation status
        $this->checkProbationStatus($student, $results);
        
        // Check for elimination criteria
        $this->checkEliminationCriteria($student, $results);

        return $results;
    }

    /**
     * Check for failing grades and issue appropriate warnings
     */
    private function checkFailingGrades($student, &$results)
    {
        $failingSubjects = $student->enrolledSubjects
            ->where('grade', '5.0')
            ->values();

        $failingCount = $failingSubjects->count();
        
        // Get existing warnings
        $existingWarnings = StudentWarning::where('student_id', $student->id)
            ->where('status', 'Active')
            ->get();

        // Rule 67.1.a.1.b - Second Warning for one major subject failed
        if ($failingCount == 1) {
            $subject = $failingSubjects->first();
            $subjectCode = $subject->subject->code ?? 'Unknown';
            
            // Check if this is a major subject (IT or NET courses)
            $isMajorSubject = preg_match('/^(IT|NET)/', $subjectCode);
            
            if ($isMajorSubject && !$this->hasWarningType($existingWarnings, 'Second Warning')) {
                $warning = StudentWarning::create([
                    'student_id' => $student->id,
                    'warning_type' => 'Second Warning',
                    'reason' => "Failed major subject: {$subjectCode} with grade 5.0",
                    'issued_date' => Carbon::now(),
                    'expiry_date' => Carbon::now()->addMonths(6),
                    'related_subject_ids' => [$subject->subject_id]
                ]);
                $results['warnings_issued'][] = $warning;
            }
        }
        
        // Rule 67.1.a.1.c - Final Warning for two failed subjects
        if ($failingCount >= 2) {
            $subjectCodes = $failingSubjects->map(function($es) {
                return $es->subject->code ?? 'Unknown';
            })->implode(', ');
            
            if (!$this->hasWarningType($existingWarnings, 'Final Warning')) {
                $warning = StudentWarning::create([
                    'student_id' => $student->id,
                    'warning_type' => 'Final Warning',
                    'reason' => "Failed {$failingCount} subjects: {$subjectCodes}",
                    'issued_date' => Carbon::now(),
                    'expiry_date' => Carbon::now()->addMonths(6),
                    'related_subject_ids' => $failingSubjects->pluck('subject_id')->toArray()
                ]);
                $results['warnings_issued'][] = $warning;
                
                // Place student on probation (Rule 67.1.a.1.c)
                $this->placeOnProbation($student, "Received Final Warning for failing {$failingCount} subjects");
            }
        }
    }

    /**
     * Check and handle incomplete grades
     */
    private function checkIncompleteGrades($student, &$results)
    {
        $incompleteSubjects = $student->enrolledSubjects
            ->where('grade', 'INC')
            ->values();

        foreach ($incompleteSubjects as $subject) {
            // Check if incomplete grade already tracked
            $existing = IncompleteGrade::where('student_id', $student->id)
                ->where('subject_id', $subject->subject_id)
                ->where('status', 'Pending')
                ->first();

            if (!$existing) {
                $incomplete = IncompleteGrade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->subject_id,
                    'grade' => 'INC',
                    'date_issued' => Carbon::now(),
                    'completion_deadline' => Carbon::now()->addMonths(8), // 2 semesters
                    'status' => 'Pending'
                ]);
                $results['incomplete_grades'][] = $incomplete;
            }
            
            // Check for expired incomplete grades (convert to 5.0)
            $this->checkExpiredIncompleteGrades($student);
        }
    }

    /**
     * Check and convert expired incomplete grades to 5.0
     */
    private function checkExpiredIncompleteGrades($student)
    {
        $expiredIncompletes = IncompleteGrade::where('student_id', $student->id)
            ->where('status', 'Pending')
            ->where('completion_deadline', '<', Carbon::now())
            ->get();

        foreach ($expiredIncompletes as $incomplete) {
            // Update the enrolled subject grade to 5.0
            EnrolledSubjects::where('student_id', $student->id)
                ->where('subject_id', $incomplete->subject_id)
                ->update(['grade' => '5.0']);
                
            // Mark incomplete as expired
            $incomplete->update([
                'status' => 'Expired',
                'final_grade' => '5.0'
            ]);
        }
    }

    /**
     * Check and update probation status
     */
    private function checkProbationStatus($student, &$results)
    {
        $probation = StudentProbation::where('student_id', $student->id)
            ->where('status', 'Active')
            ->first();

        if ($probation) {
            $results['probation_status'] = $probation;
            
            // Check if probation should be lifted (Rule 67.1.2)
            $currentGrades = EnrolledSubjects::where('student_id', $student->id)
                ->whereNotNull('grade')
                ->where('grade', '!=', 'INC')
                ->get();

            $allPassed = $currentGrades->every(function($es) {
                return floatval($es->grade) <= 3.0;
            });

            if ($allPassed && $currentGrades->isNotEmpty()) {
                $probation->update([
                    'status' => 'Completed',
                    'end_date' => Carbon::now()
                ]);
                $results['probation_ended'] = true;
            }
        }
    }

    /**
     * Check for elimination criteria (Rule 67.1.5)
     */
    private function checkEliminationCriteria($student, &$results)
    {
        // Get all failed subjects
        $failedSubjects = EnrolledSubjects::where('student_id', $student->id)
            ->where('grade', '5.0')
            ->with('subject')
            ->get();

        $failedMajorSubjects = $failedSubjects->filter(function($es) {
            $code = $es->subject->code ?? '';
            return preg_match('/^(IT|NET)/', $code);
        });

        $totalFailedUnits = $failedSubjects->sum(function($es) {
            return $es->subject->units ?? 0;
        });

        // Rule 67.1.5.a - Elimination criteria
        if ($failedMajorSubjects->count() >= 3 || $totalFailedUnits >= 9) {
            $results['elimination_warning'] = [
                'message' => 'Student meets elimination criteria: ' . 
                            ($failedMajorSubjects->count() >= 3 ? 
                             'Failed 3 or more major subjects' : 
                             'Failed 9 or more units'),
                'failed_major_count' => $failedMajorSubjects->count(),
                'failed_units' => $totalFailedUnits
            ];
        }
    }

    /**
     * Place student on probation
     */
    private function placeOnProbation($student, $reason)
    {
        $existing = StudentProbation::where('student_id', $student->id)
            ->where('status', 'Active')
            ->first();

        if (!$existing) {
            return StudentProbation::create([
                'student_id' => $student->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(6),
                'reason' => $reason,
                'status' => 'Active',
                'credit_limit' => 12 // Reduced credit load during probation
            ]);
        }

        return $existing;
    }

    /**
     * Helper: Check if student already has a specific warning type
     */
    private function hasWarningType($warnings, $type)
    {
        return $warnings->contains('warning_type', $type);
    }
}