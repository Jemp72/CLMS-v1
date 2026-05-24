<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\Laboratory;
use App\Services\BookingService;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    /**
     * Show the public reservation form (no login required).
     */
    public function create()
    {
        $laboratories = Laboratory::orderBy('lab_name')->get();

        return view('bookings.reserve', compact('laboratories'));
    }

    /**
     * Handle a reservation submission.
     * Accepts browser form submits and JSON API calls.
     */
    public function store(StoreBookingRequest $request)
    {
        $data = $request->validated();

        if ($this->bookingService->hasConflict(
            $data['lab_id'],
            $data['booking_date'],
            $data['start_time'],
            $data['end_time']
        )) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The laboratory is not available during the requested time.',
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['general' => 'The laboratory is not available during the requested time.']);
        }

        $booking = $this->bookingService->createBooking($data);

        if ($request->expectsJson()) {
            return response()->json($booking->load('laboratory'), 201);
        }

        return redirect()
            ->route('bookings.create')
            ->with('success', 'Your reservation request has been submitted and is awaiting approval.');
    }

    /**
     * Approve / reject / complete a booking.
     * Called by the admin from the /schedule view.
     */
    public function updateStatus(UpdateBookingStatusRequest $request, int $id)
    {
        $data = $request->validated();

        // Fallback to logged-in admin id from session (once real auth is wired).
        $approvedBy = $data['approved_by'] ?? session('user_id');

        $booking = $this->bookingService->updateStatus(
            $id,
            $data['booking_status'],
            $approvedBy
        );

        if ($request->expectsJson()) {
            return response()->json($booking->load(['laboratory', 'approver']));
        }

        $verbs = [
            'approved'  => 'approved',
            'rejected'  => 'rejected',
            'completed' => 'marked as completed',
        ];

        return back()->with('success', "Reservation #{$booking->booking_id} has been {$verbs[$data['booking_status']]}.");
    }
}
