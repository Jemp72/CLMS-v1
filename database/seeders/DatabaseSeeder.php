<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Laboratory;
use App\Models\SystemUser;
use App\Models\AcademicTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ─── Admin user ───────────────────────────────────────────
        SystemUser::updateOrCreate(
            ['email' => 'admin@usep.edu.ph'],
            [
                'first_name'    => 'System',
                'last_name'     => 'Administrator',
                'middle_name'   => null,
                'password_hash' => Hash::make('password'),
            ],
        );

        // ─── Laboratories (Lab 1, Lab 2, Lab 3) ───────────────────
        $labs = [
            ['lab_name' => 'Lab 1', 'location' => 'Room 301', 'capacity' => 40],
            ['lab_name' => 'Lab 2', 'location' => 'Room 302', 'capacity' => 40],
            ['lab_name' => 'Lab 3', 'location' => 'Room 303', 'capacity' => 35],
        ];

        foreach ($labs as $lab) {
            Laboratory::updateOrCreate(
                ['lab_name' => $lab['lab_name']],
                $lab,
            );
        }

        // ─── Courses ──────────────────────────────────────────────
        $courses = [
            ['course_code' => 'CS101', 'course_name' => 'Introduction to Programming'],
            ['course_code' => 'CS202', 'course_name' => 'Data Structures and Algorithms'],
            ['course_code' => 'CS301', 'course_name' => 'Software Engineering'],
            ['course_code' => 'IT201', 'course_name' => 'Database Systems'],
            ['course_code' => 'IT301', 'course_name' => 'Web Development'],
            ['course_code' => 'IT401', 'course_name' => 'Computer Networks'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['course_code' => $course['course_code']],
                $course,
            );
        }

        // ─── Academic Terms ───────────────────────────────────────
        $terms = [
            [
                'academic_year' => '2025-2026',
                'semester'      => '1st',
                'start_date'    => '2025-08-11',
                'end_date'      => '2025-12-19',
            ],
            [
                'academic_year' => '2025-2026',
                'semester'      => '2nd',
                'start_date'    => '2026-01-12',
                'end_date'      => '2026-05-22',
            ],
            [
                'academic_year' => '2025-2026',
                'semester'      => 'summer',
                'start_date'    => '2026-06-01',
                'end_date'      => '2026-07-31',
            ],
        ];

        foreach ($terms as $term) {
            AcademicTerm::updateOrCreate(
                [
                    'academic_year' => $term['academic_year'],
                    'semester'      => $term['semester'],
                ],
                $term,
            );
        }
    }
}