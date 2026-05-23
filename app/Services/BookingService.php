<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClassSchedule;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    /**
     * Fetch bookings with optional filters.
     *
     * Supported filters:
     *   - lab_id          → only bookings for that lab
     *   - status          → only bookings with that booking_status
     *   - booking_date    → only bookings on that exact date
     *   - from / to       → only bookings within a date range
     */
    public function getBookings(array $filters = []): Collection
    {
        return Booking::with(['laboratory', 'approver'])
            ->when($filters['lab_id']       ?? null, fn($q, $v) => $q->where('lab_id', $v))
            ->when($filters['status']       ?? null, fn($q, $v) => $q->where('booking_status', $v))
            ->when($filters['booking_date'] ?? null, fn($q, $v) => $q->where('booking_date', $v))
            ->when($filters['from']         ?? null, fn($q, $v) => $q->where('booking_date', '>=', $v))
            ->when($filters['to']           ?? null, fn($q, $v) => $q->where('booking_date', '<=', $v))
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Check if a lab is already booked or has a class during the requested time.
     * Returns true if there is a conflict.
     */
    public function hasConflict(int $labId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $bookingConflict = Booking::where('lab_id', $labId)
            ->where('booking_date', $date)
            ->whereIn('booking_status', ['pending', 'approved'])
            ->when($excludeBookingId, fn($q) => $q->where('booking_id', '!=', $excludeBookingId))
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })
            ->exists();

        if ($bookingConflict) {
            return true;
        }

        $dayOfWeek = now()->parse($date)->format('l'); // e.g. "Monday"

        $scheduleConflict = ClassSchedule::where('lab_id', $labId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('time_start', [$startTime, $endTime])
                  ->orWhereBetween('time_end', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('time_start', '<=', $startTime)
                         ->where('time_end', '>=', $endTime);
                  });
            })
            ->exists();

        return $scheduleConflict;
    }

    public function createBooking(array $data): Booking
    {
        return Booking::create([
            'lab_id'          => $data['lab_id'],
            'requestor_name'  => $data['requestor_name'],
            'contact_number'  => $data['contact_number'] ?? null,
            'purpose'         => $data['purpose'],
            'booking_date'    => $data['booking_date'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'booking_status'  => 'pending',
            'created_at'      => now(),
        ]);
    }

    public function updateStatus(int $bookingId, string $status, ?int $approvedBy = null): Booking
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->booking_status = $status;

        if (in_array($status, ['approved', 'rejected'])) {
            $booking->approved_by = $approvedBy;
        }

        $booking->save();

        return $booking;
    }
}
