<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Log;

class RetrainModelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle(StudentController $controller)
    {
        // Get all students
        $students = Student::all();
        $trainingData = [];

        foreach ($students as $student) {
            $features = $controller->computeStudentRiskFeatures($student->id);
            if (isset($features['error'])) {
                Log::warning("Skipping student {$student->id}: {$features['error']}");
                continue;
            }

            // Determine at_risk label (use your logic)
            $atRisk = (
                ($features['failed_subject_count'] > 0) ||
                $features['has_probation'] ||
                ($features['overall_gwa'] !== null && $features['overall_gwa'] > 3.0)
            ) ? 1 : 0;

            // Build feature array (must match Python's expected keys)
            $trainingData[] = [
                'overall_gwa'               => $features['overall_gwa'] ?? 0,
                'domain_gwa'                => $features['domain_gwa'] ?? 0,
                'programming_gpa'           => $features['programming_gpa'] ?? 0,
                'course_completion_ratio'   => $features['course_completion_ratio'] ?? 0,
                'failed_subject_count'      => $features['failed_subject_count'] ?? 0,
                'gpa_trend_slope'           => $features['gpa_trend_slope'] ?? 0,
                'has_probation'             => $features['has_probation'] ? true : false,
                'at_risk'                   => $atRisk,
            ];
        }

        if (empty($trainingData)) {
            Log::warning('No training data generated.');
            return;
        }

        // Send JSON to Python API
        $response = Http::timeout(60)->post('http://127.0.0.1:8001/retrain', [
            'students' => $trainingData
        ]);

        if ($response->successful()) {
            Log::info('Model retraining triggered successfully.');
            \Illuminate\Support\Facades\Log::info('Training data', ['data' => $trainingData]);
        } else {
            Log::error('Failed to trigger retraining: ' . $response->body());
        }
    }
}