<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use App\Services\LoggingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecentActivityLogsController extends Controller
{
    public function __construct(protected LoggingService $loggingService) {}

    public function index(Request $request)
    {
        $logs = $this->loggingService->getLogs($request);

        $entries = $logs->map(function ($log) {
            $signedOut = !is_null($log->time_out);
            $name = $log->student
                ? trim($log->student->first_name . ' ' . $log->student->last_name)
                : ($log->guest?->guest_name ?? '—');
            $personId = $log->student?->student_id ?? 'GUEST';

            return [
                'user'      => $name . ' (' . $personId . ')',
                'action'    => ($signedOut ? 'Signed out from ' : 'Signed in to ')
                               . ($log->laboratory?->lab_name ?? 'a lab')
                               . ($log->purpose ? ' — ' . $log->purpose : ''),
                'timestamp' => Carbon::parse($signedOut ? $log->time_out : $log->time_in)
                                     ->format('M d, Y h:i A'),
                'status'    => $signedOut ? 'info' : 'success',
            ];
        });

        return view('activity.index', [
            'entries'      => $entries,
            'logs'         => $logs,
            'laboratories' => Laboratory::orderBy('lab_name')->get(),
        ]);
    }
}
