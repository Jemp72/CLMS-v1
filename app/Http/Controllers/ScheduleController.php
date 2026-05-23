<?php

namespace App\Http\Controllers;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = [
            ['day' => 15, 'title' => 'CS 101 - Intro to Programming', 'time' => '8:00 AM - 10:00 AM', 'lab' => 'Lab A', 'instructor' => 'Prof. Reyes', 'contact' => '+63 912 345 6789'],
            ['day' => 15, 'title' => 'IT 201 - Database Systems', 'time' => '10:30 AM - 12:30 PM', 'lab' => 'Lab B', 'instructor' => 'Prof. Santos', 'contact' => '+63 923 456 7890'],
            ['day' => 15, 'title' => 'Research Session', 'time' => '2:00 PM - 5:00 PM', 'lab' => 'Lab A', 'instructor' => 'Dr. Cruz', 'contact' => '+63 934 567 8901'],
            ['day' => 22, 'title' => 'CS 202 - Data Structures', 'time' => '1:00 PM - 3:00 PM', 'lab' => 'Lab C', 'instructor' => 'Prof. Garcia', 'contact' => '+63 945 678 9012'],
        ];

        // May 2026 starts on Friday (index 5, Sun=0)
        $calendarDays = array_merge(array_fill(0, 5, null), range(1, 31));

        $scheduledDays = array_unique(array_column($schedules, 'day'));

        return view('schedule.index', compact('schedules', 'calendarDays', 'scheduledDays'));
    }
}