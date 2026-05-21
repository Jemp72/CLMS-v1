<?php

namespace App\Http\Controllers;

class LogbookController extends Controller
{
    public function index()
    {
        $entries = [
            ['studentId' => '2021-12345', 'name' => 'Juan Dela Cruz', 'timeIn' => '08:15 AM', 'timeOut' => '10:30 AM', 'purpose' => 'Programming Practice', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12346', 'name' => 'Maria Santos', 'timeIn' => '08:20 AM', 'timeOut' => '11:45 AM', 'purpose' => 'Research Project', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12347', 'name' => 'Pedro Garcia', 'timeIn' => '09:00 AM', 'timeOut' => '12:00 PM', 'purpose' => 'Database Assignment', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12348', 'name' => 'Ana Reyes', 'timeIn' => '09:15 AM', 'timeOut' => '', 'purpose' => 'Web Development', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12349', 'name' => 'Carlos Lopez', 'timeIn' => '09:30 AM', 'timeOut' => '', 'purpose' => 'Thesis Work', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12350', 'name' => 'Sofia Mendoza', 'timeIn' => '10:00 AM', 'timeOut' => '01:15 PM', 'purpose' => 'Java Programming', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12351', 'name' => 'Miguel Torres', 'timeIn' => '10:45 AM', 'timeOut' => '', 'purpose' => 'Study Session', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12352', 'name' => 'Isabella Cruz', 'timeIn' => '11:00 AM', 'timeOut' => '02:30 PM', 'purpose' => 'Network Lab', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12353', 'name' => 'Diego Ramos', 'timeIn' => '11:30 AM', 'timeOut' => '', 'purpose' => 'Algorithm Practice', 'date' => 'May 10, 2026'],
            ['studentId' => '2021-12354', 'name' => 'Lucia Fernandez', 'timeIn' => '01:00 PM', 'timeOut' => '04:00 PM', 'purpose' => 'Capstone Project', 'date' => 'May 10, 2026'],
        ];

        return view('logbook.index', compact('entries'));
    }
}