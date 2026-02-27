<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Http\Controllers\StudentController;
use League\Csv\Writer;

class ExportStudentFeatures extends Command
{
    protected $signature = 'students:export-features {--output=storage/app/public/train/student_features.csv}';
    protected $description = 'Export student risk features to a CSV file for Python training';

    public function handle(StudentController $controller)
    {
        $students = Student::all();
        if ($students->isEmpty()) {
            $this->error('No students found.');
            return 1;
        }

        $outputPath = $this->option('output');

        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $csv = Writer::createFromPath($outputPath, 'w+');
        
        // Define CSV header (must match the feature order expected by Python)
        $header = [
            'student_id',
            'overall_gwa',
            'domain_gwa',
            'programming_gpa',
            'course_completion_ratio',
            'failed_subject_count',
            'gpa_trend_slope',
            'has_probation',
            'at_risk'   // target variable – you need to define the logic
        ];
        $csv->insertOne($header);

        $this->info("Exporting features for " . $students->count() . " students...");

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            $features = $controller->computeStudentRiskFeatures($student->id);

            // If there's an error (student not found), skip
            if (isset($features['error'])) {
                $this->warn("Skipping student ID {$student->id}: {$features['error']}");
                $bar->advance();
                continue;
            }

            // Define the target variable "at_risk"
            // Example logic: at_risk = 1 if failed > 2 subjects OR on probation
            // $atRisk = ($features['failed_subject_count'] > 2 || $features['has_probation']) ? 1 : 0;

            //NEW
            // At‑risk if:
            // - failed at least 1 subject, OR
            // - on probation, OR
            // - overall GWA is poor (e.g., > 3.0) – in PH grading, lower is better, so >3.0 is low performance
            $atRisk = (
                $features['failed_subject_count'] > 0 || 
                $features['has_probation'] || 
                ($features['overall_gwa'] !== null && $features['overall_gwa'] > 3.0)
            ) ? 1 : 0;

            // Prepare row in the same order as header
            $row = [
                $student->id,
                $features['overall_gwa'] ?? null,
                $features['domain_gwa'] ?? null,
                $features['programming_gpa'] ?? null,
                $features['course_completion_ratio'] ?? null,
                $features['failed_subject_count'] ?? 0,
                $features['gpa_trend_slope'] ?? 0,
                $features['has_probation'] ? 1 : 0,
                $atRisk,
            ];

            $csv->insertOne($row);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Features exported to {$outputPath}");

        return 0;
    }
}

#Recent but now no need to use this command, since we are now sending data directly to the Python API instead of exporting to CSV. However, this command can still be useful for debugging or if you want to have a local copy of the features.
#php artisan students:export-features
#python train_models.py
