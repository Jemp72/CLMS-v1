<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\GuestLog;
use App\Models\LabUtilizationLog;
use App\Models\Student;
use Carbon\Carbon;

class LoggingService
{
    /**
     * Student Time In — auto-resolves lab/purpose/instructor from the
     * student's currently-active class schedule.
     */
    public function studentTimeIn(string $studentId): LabUtilizationLog
    {
        $student = Student::find($studentId);

        if (!$student) {
            throw new \Exception('Student not found.');
        }

        $existingSession = LabUtilizationLog::where('student_id', $student->student_id)
            ->whereNull('time_out')
            ->exists();

        if ($existingSession) {
            throw new \Exception('You already have an active session. Please sign out first.');
        }

        // Find a class the student is enrolled in that is happening right now.
        $now         = Carbon::now();
        $dayName     = $now->format('l');             // "Monday"
        $currentTime = $now->format('H:i:s');

        $schedule = ClassSchedule::with('course')
            ->whereHas('enrollments', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->where('day_of_week', $dayName)
            ->where('time_start', '<=', $currentTime)
            ->where('time_end',   '>=', $currentTime)
            ->first();

        if (!$schedule) {
            throw new \Exception('No class is scheduled for you in any laboratory at this time.');
        }

        $purpose = $schedule->course
            ? trim($schedule->course->course_code . ' - ' . $schedule->course->course_name)
            : 'Class session';

        return LabUtilizationLog::create([
            'student_id'    => $student->student_id,
            'purpose'       => $purpose,
            'lab_id'        => $schedule->lab_id,
            'instructor_id' => $schedule->instructor_id,
            'log_date'      => now()->toDateString(),
            'time_in'       => now(),
        ]);
    }

    /**
     * Unified Time Out — works for both students and visitors.
     *
     * Tries to match the identifier as a student number first.
     * If no student matches, treats it as a visitor name and looks up
     * an active guest session.
     */
    public function timeOut(string $identifier): LabUtilizationLog
    {
        // Try student first (by student_id).
        if (Student::find($identifier)) {
            $log = LabUtilizationLog::where('student_id', $identifier)
                ->whereNull('time_out')
                ->latest('time_in')
                ->first();

            if (!$log) {
                throw new \Exception('No active session found for this student.');
            }

            $log->update(['time_out' => now()]);

            return $log;
        }

        // Otherwise treat as a visitor name.
        $guestLogs = LabUtilizationLog::whereHas('guest', function ($q) use ($identifier) {
            $q->where('guest_name', $identifier);
        })
            ->whereNull('time_out')
            ->latest('time_in')
            ->get();

        if ($guestLogs->isEmpty()) {
            throw new \Exception('No active session found with that student number or name.');
        }

        if ($guestLogs->count() > 1) {
            throw new \Exception('Multiple active visitors share that name. Please contact laboratory staff.');
        }

        $log = $guestLogs->first();
        $log->update(['time_out' => now()]);

        return $log;
    }

    /**
     * Guest Time In — looks up the approved booking by the booker's name
     * and auto-fills lab + purpose. Each visitor is logged individually
     * (so a group of 5 produces 5 separate log entries under one booking).
     *
     * @param  string  $guestName     The visitor's own name (what gets logged).
     * @param  string  $bookedUnder   Name on the reservation (the group's booker).
     *                                If the visitor booked it themselves, this is
     *                                the same as $guestName.
     */
    public function guestTimeIn(string $guestName, string $bookedUnder): LabUtilizationLog
    {
        // Block if this visitor already has an active session.
        $activeSession = LabUtilizationLog::whereHas('guest', function ($q) use ($guestName) {
            $q->where('guest_name', $guestName);
        })
            ->whereNull('time_out')
            ->exists();

        if ($activeSession) {
            throw new \Exception('You already have an active session. Please sign out first.');
        }

        $now         = Carbon::now();
        $today       = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Approved bookings under the booker's name happening RIGHT NOW.
        $matches = Booking::where('requestor_name', $bookedUnder)
            ->where('booking_status', 'approved')
            ->whereDate('booking_date', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->get();

        if ($matches->isEmpty()) {
            $this->explainMissingBooking($bookedUnder, $today, $currentTime);
        }

        if ($matches->count() > 1) {
            throw new \Exception(
                'Multiple reservations match that booker name right now. Please contact laboratory staff.'
            );
        }

        $booking = $matches->first();

        $guest = GuestLog::create([
            'guest_name' => $guestName,
            'purpose'    => $booking->purpose,
        ]);

        return LabUtilizationLog::create([
            'guest_log_id' => $guest->guest_log_id,
            'purpose'      => $booking->purpose,
            'lab_id'       => $booking->lab_id,
            'log_date'     => $today,
            'time_in'      => now(),
        ]);
    }

    /**
     * Throw a helpful explanation when no approved booking is in the time window.
     */
    private function explainMissingBooking(string $guestName, string $today, string $currentTime): void
    {
        // Approved booking today but outside time window?
        $todayApproved = Booking::where('requestor_name', $guestName)
            ->where('booking_status', 'approved')
            ->whereDate('booking_date', $today)
            ->orderBy('start_time')
            ->first();

        if ($todayApproved) {
            throw new \Exception(
                'Your reservation is scheduled for '
                . Carbon::parse($todayApproved->start_time)->format('g:i A')
                . '. Please come back during your scheduled time.'
            );
        }

        // Pending booking?
        $pending = Booking::where('requestor_name', $guestName)
            ->where('booking_status', 'pending')
            ->whereDate('booking_date', '>=', $today)
            ->exists();

        if ($pending) {
            throw new \Exception('Your reservation is still pending approval.');
        }

        throw new \Exception(
            'No approved reservation found for that name today. '
            . 'Please book a reservation first.'
        );
    }

    /**
     * Build the logbook query with filters applied (no pagination).
     * Useful for both pagination and CSV export.
     */
    public function buildLogQuery($request)
    {
        $query = LabUtilizationLog::with([
            'student',
            'guest',
            'laboratory',
            'instructor',
        ]);

        // Search across purpose + student fields + guest name.
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
                        $guestQuery->where('guest_name', 'like', "%{$search}%");
                    });
            });
        }

        // Date window filter.
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

        // Laboratory filter.
        if ($request->filled('lab_id')) {
            $query->where('lab_id', $request->lab_id);
        }

        // Specific date filter.
        if ($request->filled('date')) {
            $query->whereDate('log_date', $request->date);
        }

        return $query->latest('time_in');
    }

    /**
     * Get paginated logbook entries.
     */
    public function getLogs($request)
    {
        return $this->buildLogQuery($request)
            ->paginate(10)
            ->withQueryString();
    }

}