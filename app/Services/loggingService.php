<?php

namespace App\Services;

use App\Models\GuestLog;
use App\Models\LabUtilizationLog;
use App\Models\Student;
use Carbon\Carbon;

class LoggingService
{
    /**
     * Student Time In
     */
    public function studentTimeIn(array $data): LabUtilizationLog
    {
        $student = Student::find($data['student_id']);

        if (!$student) {
            throw new \Exception('Student not found.');
        }

        $existingSession = LabUtilizationLog::where('student_id', $student->student_id)
            ->whereNull('time_out')
            ->exists();

        if ($existingSession) {
            throw new \Exception('Student already has an active session.');
        }

        return LabUtilizationLog::create([
            'student_id' => $student->student_id,
            'purpose' => $data['purpose'],
            'lab_id' => $data['lab_id'],
            'instructor_id' => $data['instructor_id'] ?? null,
            'log_date' => now()->toDateString(),
            'time_in' => now(),
        ]);
    }

    /**
     * Student Time Out
     */
    public function studentTimeOut(string $studentId): LabUtilizationLog
    {
        $log = LabUtilizationLog::where('student_id', $studentId)
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        if (!$log) {
            throw new \Exception('No active session found.');
        }

        $log->update([
            'time_out' => now(),
        ]);

        return $log;
    }

    /**
     * Guest Time In
     */
    public function guestTimeIn(array $data): LabUtilizationLog
    {
        $guest = GuestLog::create([
            'guest_name' => $data['guest_name'],
            'organization' => $data['organization'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'purpose' => $data['purpose'],
        ]);

        return LabUtilizationLog::create([
            'guest_log_id' => $guest->guest_log_id,
            'purpose' => $data['purpose'],
            'lab_id' => $data['lab_id'],
            'log_date' => now()->toDateString(),
            'time_in' => now(),
        ]);
    }

    /**
     * Get Logbook Entries
     */
    public function getLogs($request)
    {
        $query = LabUtilizationLog::with([
            'student',
            'guest',
            'laboratory',
            'instructor'
        ]);

        /**
         * Search
         */
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('purpose', 'like', "%{$search}%")

                    ->orWhereHas('student', function ($studentQuery) use ($search) {

                        $studentQuery
                            ->where('student_id', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('guest', function ($guestQuery) use ($search) {

                        $guestQuery
                            ->where('guest_name', 'like', "%{$search}%");
                    });
            });
        }

        /**
         * Filter By Date
         */
        if ($request->filled('filter')) {

            switch ($request->filter) {

                case 'day':

                    $query->whereDate('log_date', today());

                    break;

                case 'week':

                    $query->whereBetween('log_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);

                    break;

                case 'month':

                    $query->whereMonth('log_date', now()->month)
                        ->whereYear('log_date', now()->year);

                    break;
            }
        }

        return $query
            ->latest('time_in')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Get Active Users
     */
    public function getActiveUsers()
    {
        return LabUtilizationLog::with([
            'student',
            'guest',
            'laboratory'
        ])
            ->whereNull('time_out')
            ->latest('time_in')
            ->get();
    }
}