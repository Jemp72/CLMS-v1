<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\UploadedFile;

class EnrollmentService
{
    /**
     * Required CSV columns. Header row must include at least these.
     */
    public const REQUIRED_COLUMNS = ['student_id', 'first_name', 'last_name'];

    /**
     * Optional CSV columns (used if present, otherwise null).
     */
    public const OPTIONAL_COLUMNS = ['middle_name', 'suffix'];

    /**
     * Import a class list from a CSV file and enroll every row into each
     * of the given class schedules.
     *
     * @param  UploadedFile  $file               The uploaded CSV.
     * @param  int[]         $classScheduleIds   One or more class_schedule_id values.
     * @return array  Counts + per-row errors.
     */
    public function importFromCsv(UploadedFile $file, array $classScheduleIds): array
    {
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            throw new \Exception('Unable to read the uploaded file.');
        }

        // First row is the header.
        $headers = fgetcsv($handle);
        if (!is_array($headers)) {
            fclose($handle);
            throw new \Exception('The CSV file is empty.');
        }

        // Normalise header names (trim + lowercase).
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), $headers);

        // Make sure every required column is present.
        foreach (self::REQUIRED_COLUMNS as $col) {
            if (!in_array($col, $headers, true)) {
                fclose($handle);
                throw new \Exception(
                    'CSV is missing required column: ' . $col
                    . '. Required columns: ' . implode(', ', self::REQUIRED_COLUMNS)
                );
            }
        }

        $headerMap = array_flip($headers);

        $createdStudents   = 0;
        $studentsProcessed = 0;
        $enrolled          = 0;
        $skippedExisting   = 0;
        $errors            = [];
        $rowNumber         = 1; // header is row 1

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely empty rows.
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $studentId = trim((string) ($row[$headerMap['student_id']] ?? ''));
            $firstName = trim((string) ($row[$headerMap['first_name']] ?? ''));
            $lastName  = trim((string) ($row[$headerMap['last_name']]  ?? ''));

            if ($studentId === '' || $firstName === '' || $lastName === '') {
                $errors[] = "Row {$rowNumber}: missing student_id, first_name, or last_name.";
                continue;
            }

            // Find or create the student.
            $student = Student::find($studentId);
            if (!$student) {
                $student = Student::create([
                    'student_id'  => $studentId,
                    'first_name'  => $firstName,
                    'last_name'   => $lastName,
                    'middle_name' => $this->optional($row, $headerMap, 'middle_name'),
                    'suffix'      => $this->optional($row, $headerMap, 'suffix'),
                ]);
                $createdStudents++;
            }
            $studentsProcessed++;

            // Enrol the student in every selected class — skip if already enrolled.
            foreach ($classScheduleIds as $classScheduleId) {
                $exists = StudentEnrollment::where('student_id', $studentId)
                    ->where('class_schedule_id', $classScheduleId)
                    ->exists();

                if ($exists) {
                    $skippedExisting++;
                } else {
                    StudentEnrollment::create([
                        'student_id'        => $studentId,
                        'class_schedule_id' => $classScheduleId,
                    ]);
                    $enrolled++;
                }
            }
        }

        fclose($handle);

        return [
            'students_processed' => $studentsProcessed,
            'classes_targeted'   => count($classScheduleIds),
            'enrolled'           => $enrolled,
            'created_students'   => $createdStudents,
            'skipped_existing'   => $skippedExisting,
            'errors'             => $errors,
        ];
    }

    /**
     * Safely pull an optional column from a CSV row.
     */
    private function optional(array $row, array $headerMap, string $column): ?string
    {
        if (!isset($headerMap[$column])) {
            return null;
        }
        $value = trim((string) ($row[$headerMap[$column]] ?? ''));
        return $value === '' ? null : $value;
    }
}
