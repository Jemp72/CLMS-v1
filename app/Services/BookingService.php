<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    /**
     * Fetch all bookings for a specific month, formatted for the calendar view.
     * Returns plain arrays (already shaped for Alpine consumption).
     */
    public function getBookingsForCalendar(int $month, int $year): Collection
    {
        return Booking::with('laboratory')
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->orderBy('start_time')
            ->get()
            ->map(fn($b) => [
                'id'        => $b->booking_id,
                'day'       => (int) $b->booking_date->format('j'),
                'lab_id'    => $b->lab_id,
                'lab'       => optional($b->laboratory)->lab_name ?? '—',
                'title'     => $b->purpose,
                'time'      => Carbon::parse($b->start_time)->format('g:i A')
                               . ' – '
                               . Carbon::parse($b->end_time)->format('g:i A'),
                'requestor' => $b->requestor_name,
                'contact'   => $b->contact_number ?? '—',
                'status'    => $b->booking_status,
            ]);
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
