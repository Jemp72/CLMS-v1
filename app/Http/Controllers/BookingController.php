<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Services\BookingService;
use Illuminate\Http\Request;

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
    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'booking_status' => 'required|in:approved,rejected,completed',
            'approved_by'    => 'nullable|integer|exists:system_users,system_user_id',
        ]);

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
