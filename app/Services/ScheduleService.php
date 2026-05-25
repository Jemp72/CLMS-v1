<?php

namespace App\Services;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleService
{
    /**
     * Expand weekly recurring class schedules into per-date entries for a given month.
     *
     * Example: a "Monday 8:00-10:00" class appears on every Monday in that month.
     * Returns an array shaped exactly like booking entries for the calendar.
     */
    public function getSchedulesForCalendar(int $month, int $year): Collection
    {
        $schedules = ClassSchedule::with(['course', 'instructor', 'laboratory'])->get();

        $result      = collect();
        $start       = Carbon::create($year, $month, 1);
        $daysInMonth = $start->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayName = Carbon::create($year, $month, $day)->format('l'); // "Monday"

            foreach ($schedules as $schedule) {
                if ($schedule->day_of_week !== $dayName) {
                    continue;
                }

                $course = $schedule->course;
                $title  = trim(($course->course_code ?? '') . ' - ' . ($course->course_name ?? ''));

                $instructor = $schedule->instructor;
                $instName   = $instructor
                    ? trim($instructor->first_name . ' ' . $instructor->last_name)
                    : '—';

                $result->push([
                    'id'         => $schedule->class_schedule_id,
                    'day'        => $day,
                    'lab_id'     => $schedule->lab_id,
                    'lab'        => optional($schedule->laboratory)->lab_name ?? '—',
                    'title'      => $title ?: 'Untitled Course',
                    'time'       => Carbon::parse($schedule->time_start)->format('g:i A')
                                    . ' – '
                                    . Carbon::parse($schedule->time_end)->format('g:i A'),
                    'instructor' => $instName,
                    'contact'    => optional($instructor)->email ?? '—',
                ]);
            }
        }

        return $result;
    }

    /**
     * Check if the lab already has a class scheduled at the requested time slot.
     */
    public function hasConflict(int $labId, string $dayOfWeek, string $startTime, string $endTime, int $termId, ?int $excludeId = null): bool
    {
        return ClassSchedule::where('lab_id', $labId)
            ->where('day_of_week', $dayOfWeek)
            ->where('term_id', $termId)
            ->when($excludeId, fn($q) => $q->where('class_schedule_id', '!=', $excludeId))
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('time_start', [$startTime, $endTime])
                  ->orWhereBetween('time_end', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('time_start', '<=', $startTime)
                         ->where('time_end', '>=', $endTime);
                  });
            })
            ->exists();
    }

    public function createSchedule(array $data): ClassSchedule
    {
        return ClassSchedule::create($data);
    }
}
