<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestTimeInRequest;
use App\Http\Requests\StudentTimeInRequest;
use App\Http\Requests\StudentTimeOutRequest;
use App\Models\LabUtilizationLog;
use App\Models\Laboratory;
use App\Services\LoggingService;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    protected LoggingService $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Public logging terminal page
     */
    public function index()
    {
        $laboratories = Laboratory::all();

        return view('logging.index', compact('laboratories'));
    }

    /**
     * Student Time In
     */
    public function studentTimeIn(StudentTimeInRequest $request)
    {
        try {

            $this->loggingService->studentTimeIn(
                $request->validated()
            );

            return redirect()
                ->back()
                ->with('success', 'Student successfully logged in.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Student Time Out
     */
    public function studentTimeOut(StudentTimeOutRequest $request)
    {
        try {

            $this->loggingService->studentTimeOut(
                $request->student_id
            );

            return redirect()
                ->back()
                ->with('success', 'Student successfully logged out.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Guest Time In
     */
    public function guestTimeIn(GuestTimeInRequest $request)
    {
        try {

            $this->loggingService->guestTimeIn(
                $request->validated()
            );

            return redirect()
                ->back()
                ->with('success', 'Guest successfully logged in.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Admin Logbook
     */
    public function logs(Request $request)
    {
        $logs = $this->loggingService
            ->getLogs($request);

        $entries = $logs->map(function ($log) {

            return [
                'id' => $log->lab_utilization_log_id,

                'studentId' => $log->student?->student_id
                    ?? 'GUEST',

                'name' => $log->student
                    ? $log->student->first_name . ' ' . $log->student->last_name
                    : $log->guest?->guest_name,

                'timeIn' => optional($log->time_in)
                    ->format('h:i A'),

                'timeOut' => $log->time_out
                    ? $log->time_out->format('h:i A')
                    : null,

                'purpose' => $log->purpose,

                'date' => optional($log->log_date)
                    ->format('M d, Y'),

                'laboratory' => $log->laboratory?->lab_name,
            ];
        });

        return view('logbook.index', [
            'entries' => $entries,
            'logs' => $logs,
        ]);
    }

    /**
     * Active Users
     */
    public function activeUsers()
    {
        $activeUsers = $this->loggingService->getActiveUsers();

        return view('logging.active-users', compact('activeUsers'));
    }

    /**
     * View Single Log
     */
    public function show($id)
    {
        $log = LabUtilizationLog::with([
            'student',
            'guest',
            'laboratory',
            'instructor'
        ])->findOrFail($id);

        return view('logging.show', compact('log'));
    }
}