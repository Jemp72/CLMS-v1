<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Laboratory;
use App\Services\BookingService;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected ScheduleService $scheduleService,
    ) {}

    /**
     * Show the calendar view with class schedules + bookings.
     * Accepts ?month=YYYY-MM for navigation; defaults to current month.
     */
    public function index(Request $request)
    {
        // Parse the month cursor from URL (?month=YYYY-MM) or default to today's month.
        $monthInput = $request->query('month');
        $cursor     = ($monthInput && preg_match('/^\d{4}-\d{1,2}$/', $monthInput))
            ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
            : Carbon::now()->startOfMonth();

        $currentMonth = $cursor->month;
        $currentYear  = $cursor->year;

        // Prev / next month strings for the chevron links.
        $prevMonth   = $cursor->copy()->subMonth()->format('Y-m');
        $nextMonth   = $cursor->copy()->addMonth()->format('Y-m');
        $monthLabel  = $cursor->format('F Y');   // "May 2026"
        $monthShort  = $cursor->format('M');     // "May"
        $displayYear = $cursor->year;            // 2026

        // Real class schedules + real bookings — both via their services.
        $schedules = $this->scheduleService->getSchedulesForCalendar($currentMonth, $currentYear);
        $bookings  = $this->bookingService->getBookingsForCalendar($currentMonth, $currentYear);

        // Real laboratories — feeds the lab filter dropdown.
        $laboratories = Laboratory::orderBy('lab_name')->get();

        $scheduledDays = $schedules->pluck('day')->unique()->values();

        // Calendar grid for the displayed month.
        $firstDayOfMonth = $cursor->dayOfWeek; // 0 = Sun
        $daysInMonth     = $cursor->daysInMonth;
        $calendarDays    = array_merge(array_fill(0, $firstDayOfMonth, null), range(1, $daysInMonth));

        // Default selected day: today if viewing real-current month, otherwise day 1.
        $now      = Carbon::now();
        $todayDay = ($currentMonth === $now->month && $currentYear === $now->year)
            ? $now->day
            : 1;

        return view('schedule.index', compact(
            'schedules',
            'calendarDays',
            'scheduledDays',
            'bookings',
            'laboratories',
            'prevMonth',
            'nextMonth',
            'monthLabel',
            'monthShort',
            'displayYear',
            'todayDay',
        ));
    }

    /**
     * Show the Add Class Schedule form.
     */
    public function create()
    {
        return view('schedule.create', [
            'courses'      => Course::orderBy('course_code')->get(),
            'instructors'  => Instructor::orderBy('last_name')->orderBy('first_name')->get(),
            'laboratories' => Laboratory::orderBy('lab_name')->get(),
            'terms'        => AcademicTerm::orderBy('start_date', 'desc')->get(),
        ]);
    }

    /**
     * Save a new class schedule.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'     => 'required|integer|exists:courses,course_id',
            'instructor_id' => 'required|integer|exists:instructors,instructor_id',
            'lab_id'        => 'required|integer|exists:laboratories,lab_id',
            'day_of_week'   => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'time_start'    => 'required|date_format:H:i',
            'time_end'      => 'required|date_format:H:i|after:time_start',
            'term_id'       => 'required|integer|exists:academic_terms,academic_term_id',
        ]);

        if ($this->scheduleService->hasConflict(
            $data['lab_id'],
            $data['day_of_week'],
            $data['time_start'],
            $data['time_end'],
            $data['term_id']
        )) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'This laboratory already has a class scheduled at the requested time.']);
        }

        $this->scheduleService->createSchedule($data);

        return redirect()
            ->route('schedule')
            ->with('success', 'Class schedule has been added successfully.');
    }
}
