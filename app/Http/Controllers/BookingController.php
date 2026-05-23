<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Laboratory;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'lab_id'       => 'nullable|integer|exists:laboratories,lab_id',
            'status'       => 'nullable|in:pending,approved,rejected,completed',
            'booking_date' => 'nullable|date',
            'from'         => 'nullable|date',
            'to'           => 'nullable|date|after_or_equal:from',
        ]);

        $bookings = $this->bookingService->getBookings($filters);

        return response()->json($bookings);
    }

    /**
     * Convenience endpoint for fetching bookings of a specific lab.
     */
    public function byLab(int $labId, Request $request)
    {
        $filters = $request->validate([
            'status'       => 'nullable|in:pending,approved,rejected,completed',
            'booking_date' => 'nullable|date',
            'from'         => 'nullable|date',
            'to'           => 'nullable|date|after_or_equal:from',
        ]);

        $filters['lab_id'] = $labId;

        return response()->json($this->bookingService->getBookings($filters));
    }

    public function show(int $id)
    {
        $booking = Booking::with(['laboratory', 'approver'])->findOrFail($id);

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lab_id'         => 'required|integer|exists:laboratories,lab_id',
            'requestor_name' => 'required|string|max:150',
            'contact_number' => 'nullable|string|max:50',
            'purpose'        => 'required|string|max:255',
            'booking_date'   => 'required|date|after_or_equal:today',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
        ]);

        if ($this->bookingService->hasConflict(
            $data['lab_id'],
            $data['booking_date'],
            $data['start_time'],
            $data['end_time']
        )) {
            return response()->json([
                'message' => 'The laboratory is not available during the requested time.',
            ], 422);
        }

        $booking = $this->bookingService->createBooking($data);

        return response()->json($booking->load('laboratory'), 201);
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'booking_status' => 'required|in:approved,rejected,completed',
            'approved_by'    => 'nullable|integer|exists:system_users,system_user_id',
        ]);

        $booking = $this->bookingService->updateStatus(
            $id,
            $data['booking_status'],
            $data['approved_by'] ?? null
        );

        return response()->json($booking->load(['laboratory', 'approver']));
    }

    public function destroy(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json(['message' => 'Booking deleted.']);
    }
}
