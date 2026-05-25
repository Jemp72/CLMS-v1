<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestTimeInRequest;
use App\Http\Requests\StudentTimeInRequest;
use App\Http\Requests\TimeOutRequest;
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
     * Student Time In — only requires the student number.
     * The service auto-fills lab / purpose / instructor from the active class.
     */
    public function studentTimeIn(StudentTimeInRequest $request)
    {
        try {

            $this->loggingService->studentTimeIn(
                $request->student_id
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
     * Unified Time Out — handles both students and visitors.
     */
    public function timeOut(TimeOutRequest $request)
    {
        try {

            $this->loggingService->timeOut(
                $request->identifier
            );

            return redirect()
                ->back()
                ->with('success', 'Successfully signed out. Have a great day!');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Guest Time In — uses the approved booking to auto-fill lab + purpose.
     * Supports group reservations via the optional "booked_under" field.
     */
    public function guestTimeIn(GuestTimeInRequest $request)
    {
        try {

            $this->loggingService->guestTimeIn(
                $request->guest_name,
                $request->booked_under,
            );

            return redirect()
                ->back()
                ->with('success', 'Welcome! You are now signed in.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Admin Logbook — paginated table of lab usage entries.
     * Supports ?search, ?filter, ?lab_id query params and ?export=csv.
     */
    public function logs(Request $request)
    {
        // CSV export — fetch ALL filtered rows (no pagination).
        if ($request->export === 'csv') {
            return $this->exportCsv($request);
        }

        $logs = $this->loggingService->getLogs($request);

        $entries = $logs->map(function ($log) {
            return [
                'id'         => $log->lab_utilization_log_id,
                'studentId'  => $log->student?->student_id ?? 'GUEST',
                'name'       => $log->student
                                ? trim($log->student->first_name . ' ' . $log->student->last_name)
                                : ($log->guest?->guest_name ?? '—'),
                'timeIn'     => optional($log->time_in)->format('h:i A'),
                'timeOut'    => $log->time_out ? $log->time_out->format('h:i A') : null,
                'purpose'    => $log->purpose,
                'date'       => optional($log->log_date)->format('M d, Y'),
                'laboratory' => $log->laboratory?->lab_name ?? '—',
            ];
        });

        $laboratories = Laboratory::orderBy('lab_name')->get();

        return view('logbook.index', [
            'entries'      => $entries,
            'logs'         => $logs,
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Stream all currently-filtered logs as a CSV download.
     */
    private function exportCsv(Request $request)
    {
        $filename = 'logbook-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Date', 'Student ID', 'Name', 'Laboratory', 'Purpose', 'Time In', 'Time Out',
            ]);

            $this->loggingService->buildLogQuery($request)
                ->chunk(200, function ($chunk) use ($handle) {
                    foreach ($chunk as $log) {
                        fputcsv($handle, [
                            optional($log->log_date)->format('Y-m-d'),
                            $log->student?->student_id ?? 'GUEST',
                            $log->student
                                ? trim($log->student->first_name . ' ' . $log->student->last_name)
                                : ($log->guest?->guest_name ?? ''),
                            $log->laboratory?->lab_name ?? '',
                            $log->purpose ?? '',
                            optional($log->time_in)->format('Y-m-d H:i:s'),
                            $log->time_out ? $log->time_out->format('Y-m-d H:i:s') : '',
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

}