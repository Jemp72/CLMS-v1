<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportEnrollmentRequest;
use App\Models\ClassSchedule;
use App\Services\EnrollmentService;

class EnrollmentController extends Controller
{
    public function __construct(protected EnrollmentService $enrollmentService) {}

    /**
     * Class List index — every class schedule, expandable to see enrolled students.
     */
    public function index()
    {
        $schedules = ClassSchedule::with([
                'course',
                'instructor',
                'laboratory',
                'term',
                'enrollments.student',
            ])
            ->get()
            ->sortBy(fn($s) => [
                optional($s->course)->course_code ?? '',
                $s->day_of_week,
                (string) $s->time_start,
            ])
            ->values();

        return view('enrollments.index', compact('schedules'));
    }

    /**
     * Show the CSV upload form (admin only).
     */
    public function create()
    {
        $schedules = ClassSchedule::with(['course', 'instructor', 'laboratory', 'term'])
            ->orderBy('day_of_week')
            ->orderBy('time_start')
            ->get();

        return view('enrollments.import', compact('schedules'));
    }

    /**
     * Process the CSV upload.
     */
    public function store(ImportEnrollmentRequest $request)
    {
        $data = $request->validated();

        try {
            $result = $this->enrollmentService->importFromCsv(
                $request->file('csv_file'),
                array_map('intval', $data['class_schedule_ids']),
            );
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        $summary = sprintf(
            'Processed %d student(s) across %d class(es). Created %d enrollment(s). %d new student record(s). %d already enrolled (skipped).',
            $result['students_processed'],
            $result['classes_targeted'],
            $result['enrolled'],
            $result['created_students'],
            $result['skipped_existing'],
        );

        return back()
            ->with('success', $summary)
            ->with('row_errors', $result['errors']);
    }
}
