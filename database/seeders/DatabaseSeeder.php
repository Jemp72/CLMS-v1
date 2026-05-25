<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\Booking;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\GuestLog;
use App\Models\Instructor;
use App\Models\LabUtilizationLog;
use App\Models\Laboratory;
use App\Models\SystemUser;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ─── 1. Admin user ────────────────────────────────────────────
        SystemUser::updateOrCreate(
            ['email' => 'admin@usep.edu.ph'],
            [
                'first_name'    => 'System',
                'last_name'     => 'Administrator',
                'middle_name'   => null,
                'password_hash' => Hash::make('password'),
            ],
        );

        // ─── 2. Laboratories ──────────────────────────────────────────
        $labs = collect([
            ['lab_name' => 'Lab 1', 'location' => 'Room 301', 'capacity' => 40],
            ['lab_name' => 'Lab 2', 'location' => 'Room 302', 'capacity' => 40],
            ['lab_name' => 'Lab 3', 'location' => 'Room 303', 'capacity' => 35],
        ])->map(fn ($l) => Laboratory::updateOrCreate(
            ['lab_name' => $l['lab_name']],
            $l,
        ));

        // ─── 3. Courses ───────────────────────────────────────────────
        $courses = collect([
            ['course_code' => 'CS101', 'course_name' => 'Introduction to Programming'],
            ['course_code' => 'CS202', 'course_name' => 'Data Structures and Algorithms'],
            ['course_code' => 'CS301', 'course_name' => 'Software Engineering'],
            ['course_code' => 'IT201', 'course_name' => 'Database Systems'],
            ['course_code' => 'IT301', 'course_name' => 'Web Development'],
            ['course_code' => 'IT401', 'course_name' => 'Computer Networks'],
        ])->map(fn ($c) => Course::updateOrCreate(
            ['course_code' => $c['course_code']],
            $c,
        ));

        // ─── 4. Academic Terms ────────────────────────────────────────
        $terms = collect([
            ['academic_year' => '2025-2026', 'semester' => '1st',    'start_date' => '2025-08-11', 'end_date' => '2025-12-19'],
            ['academic_year' => '2025-2026', 'semester' => '2nd',    'start_date' => '2026-01-12', 'end_date' => '2026-05-22'],
            ['academic_year' => '2025-2026', 'semester' => 'summer', 'start_date' => '2026-06-01', 'end_date' => '2026-07-31'],
        ])->map(fn ($t) => AcademicTerm::updateOrCreate(
            ['academic_year' => $t['academic_year'], 'semester' => $t['semester']],
            $t,
        ));

        // ─── 5. Instructors ───────────────────────────────────────────
        $instructors = collect([
            ['first_name' => 'Juan',  'last_name' => 'Reyes',  'middle_name' => 'Dela Cruz', 'email' => 'jreyes@usep.edu.ph',  'contact_number' => '+63 912 345 6789'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'middle_name' => 'Lim',       'email' => 'msantos@usep.edu.ph', 'contact_number' => '+63 923 456 7890'],
            ['first_name' => 'Pedro', 'last_name' => 'Garcia', 'middle_name' => null,        'email' => 'pgarcia@usep.edu.ph', 'contact_number' => null],
            ['first_name' => 'Ana',   'last_name' => 'Cruz',   'middle_name' => 'Mendoza',   'email' => 'acruz@usep.edu.ph',   'contact_number' => '+63 934 567 8901'],
        ])->map(fn ($i) => Instructor::updateOrCreate(
            ['email' => $i['email']],
            $i,
        ));

        // ─── 6. Equipment (a few per lab, mixed statuses) ─────────────
        $eqRows = [
            ['equipment_no' => 'EQ-001', 'serial_no' => 'SN-A001', 'equipment_name' => 'Desktop PC', 'brand' => 'Dell',    'model_number' => 'OptiPlex 7090',  'equipment_type' => 'computer_unit', 'equipment_status' => 'available',   'quantity' => 1, 'lab_id' => $labs[0]->lab_id, 'preventive_maintenance_done' => 1, 'calibration_done' => 0, 'remarks' => null],
            ['equipment_no' => 'EQ-002', 'serial_no' => 'SN-A002', 'equipment_name' => 'Desktop PC', 'brand' => 'Dell',    'model_number' => 'OptiPlex 7090',  'equipment_type' => 'computer_unit', 'equipment_status' => 'in-use',      'quantity' => 1, 'lab_id' => $labs[0]->lab_id, 'preventive_maintenance_done' => 1, 'calibration_done' => 0, 'remarks' => null],
            ['equipment_no' => 'EQ-003', 'serial_no' => 'SN-A003', 'equipment_name' => 'Projector',  'brand' => 'Epson',   'model_number' => 'EB-X06',         'equipment_type' => 'peripheral',    'equipment_status' => 'available',   'quantity' => 1, 'lab_id' => $labs[0]->lab_id, 'preventive_maintenance_done' => 0, 'calibration_done' => 1, 'remarks' => null],
            ['equipment_no' => 'EQ-004', 'serial_no' => 'SN-B001', 'equipment_name' => 'Desktop PC', 'brand' => 'HP',      'model_number' => 'ProDesk 400',    'equipment_type' => 'computer_unit', 'equipment_status' => 'available',   'quantity' => 1, 'lab_id' => $labs[1]->lab_id, 'preventive_maintenance_done' => 1, 'calibration_done' => 0, 'remarks' => null],
            ['equipment_no' => 'EQ-005', 'serial_no' => 'SN-B002', 'equipment_name' => 'Network Switch','brand' => 'Cisco','model_number' => 'Catalyst 2960',  'equipment_type' => 'component',     'equipment_status' => 'available',   'quantity' => 1, 'lab_id' => $labs[1]->lab_id, 'preventive_maintenance_done' => 1, 'calibration_done' => 1, 'remarks' => null],
            ['equipment_no' => 'EQ-006', 'serial_no' => 'SN-B003', 'equipment_name' => 'Printer',     'brand' => 'Brother','model_number' => 'HL-L2350DW',    'equipment_type' => 'peripheral',    'equipment_status' => 'maintenance', 'quantity' => 1, 'lab_id' => $labs[1]->lab_id, 'preventive_maintenance_done' => 0, 'calibration_done' => 0, 'remarks' => 'Paper jam — pending parts'],
            ['equipment_no' => 'EQ-007', 'serial_no' => 'SN-C001', 'equipment_name' => 'Desktop PC', 'brand' => 'Lenovo',  'model_number' => 'ThinkCentre M70','equipment_type' => 'computer_unit', 'equipment_status' => 'damaged',     'quantity' => 1, 'lab_id' => $labs[2]->lab_id, 'preventive_maintenance_done' => 0, 'calibration_done' => 0, 'remarks' => 'Power supply failure'],
            ['equipment_no' => 'EQ-008', 'serial_no' => null,      'equipment_name' => 'Whiteboard',  'brand' => null,     'model_number' => null,             'equipment_type' => 'miscellaneous', 'equipment_status' => 'available',   'quantity' => 2, 'lab_id' => $labs[2]->lab_id, 'preventive_maintenance_done' => 0, 'calibration_done' => 0, 'remarks' => null],
        ];
        foreach ($eqRows as $row) {
            DB::table('equipments')->updateOrInsert(
                ['equipment_no' => $row['equipment_no']],
                $row,
            );
        }

        // ─── 7. Office supplies (mixed stock levels) ──────────────────
        $supRows = [
            ['supply_name' => 'Whiteboard Marker',   'category' => 'Stationery',   'status' => 'fully_stocked', 'unit' => 'box',  'remarks' => null],
            ['supply_name' => 'Ballpen (black)',     'category' => 'Stationery',   'status' => 'in_stock',      'unit' => 'pcs',  'remarks' => null],
            ['supply_name' => 'Bond Paper A4',       'category' => 'Stationery',   'status' => 'low_stock',     'unit' => 'ream', 'remarks' => 'Order before semester ends'],
            ['supply_name' => 'Toner Cartridge',     'category' => 'Ink & Toner',  'status' => 'low_stock',     'unit' => 'pcs',  'remarks' => null],
            ['supply_name' => 'HDMI Cable',          'category' => 'Cables',       'status' => 'in_stock',      'unit' => 'pcs',  'remarks' => null],
            ['supply_name' => 'Ethernet Cable Cat6', 'category' => 'Cables',       'status' => 'out_of_stock',  'unit' => 'meter','remarks' => 'Last batch consumed'],
            ['supply_name' => 'Cleaning Spray',      'category' => 'Cleaning',     'status' => 'fully_stocked', 'unit' => 'bottle','remarks' => null],
            ['supply_name' => 'Screwdriver Set',     'category' => 'Tools',        'status' => 'in_stock',      'unit' => 'set',  'remarks' => null],
        ];
        foreach ($supRows as $row) {
            DB::table('office_supplies')->updateOrInsert(
                ['supply_name' => $row['supply_name']],
                $row,
            );
        }

        // ─── 8. Class schedules ──────────────────────────────────────
        // Wipe + reseed so dates/days stay coherent on re-runs.
        DB::table('class_schedule')->delete();

        $currentTerm = $terms[1]; // 2025-2026 / 2nd

        $schedules = [
            ['course' => 'CS101', 'instructor' => 'jreyes@usep.edu.ph', 'lab' => 'Lab 1', 'day' => 'Monday',    'start' => '08:00:00', 'end' => '10:00:00'],
            ['course' => 'IT201', 'instructor' => 'msantos@usep.edu.ph','lab' => 'Lab 2', 'day' => 'Monday',    'start' => '10:30:00', 'end' => '12:30:00'],
            ['course' => 'CS202', 'instructor' => 'pgarcia@usep.edu.ph','lab' => 'Lab 1', 'day' => 'Wednesday', 'start' => '13:00:00', 'end' => '15:00:00'],
            ['course' => 'IT301', 'instructor' => 'acruz@usep.edu.ph',  'lab' => 'Lab 3', 'day' => 'Friday',    'start' => '09:00:00', 'end' => '11:00:00'],
        ];
        foreach ($schedules as $s) {
            ClassSchedule::create([
                'course_id'     => $courses->firstWhere('course_code', $s['course'])->course_id,
                'instructor_id' => $instructors->firstWhere('email', $s['instructor'])->instructor_id,
                'lab_id'        => $labs->firstWhere('lab_name', $s['lab'])->lab_id,
                'day_of_week'   => $s['day'],
                'time_start'    => $s['start'],
                'time_end'      => $s['end'],
                'term_id'       => $currentTerm->academic_term_id,
            ]);
        }

        // ─── 9. Bookings (mix of statuses) ────────────────────────────
        DB::table('bookings')->delete();

        $bookings = [
            ['lab' => 'Lab 1', 'requestor' => 'Dr. Rodriguez',  'contact' => '+63 917 555 1234', 'purpose' => 'Faculty research meeting',  'date' => Carbon::today()->addDays(2),  'start' => '14:00:00', 'end' => '16:00:00', 'status' => 'pending'],
            ['lab' => 'Lab 2', 'requestor' => 'Atty. Castillo', 'contact' => '+63 918 555 5678', 'purpose' => 'Accreditation review',      'date' => Carbon::today()->addDays(5),  'start' => '09:00:00', 'end' => '12:00:00', 'status' => 'pending'],
            ['lab' => 'Lab 3', 'requestor' => 'IT Society',     'contact' => '+63 919 555 9012', 'purpose' => 'Code-a-thon practice',      'date' => Carbon::today()->addDay(),    'start' => '13:00:00', 'end' => '17:00:00', 'status' => 'approved'],
            ['lab' => 'Lab 1', 'requestor' => 'Vendor Demo',    'contact' => null,                'purpose' => 'Equipment demonstration',   'date' => Carbon::today()->subDays(3),  'start' => '10:00:00', 'end' => '11:00:00', 'status' => 'completed'],
            ['lab' => 'Lab 2', 'requestor' => 'External Group', 'contact' => null,                'purpose' => 'Training (denied — conflict)', 'date' => Carbon::today()->addDays(7), 'start' => '08:00:00', 'end' => '10:00:00', 'status' => 'rejected'],
        ];
        foreach ($bookings as $b) {
            Booking::create([
                'lab_id'         => $labs->firstWhere('lab_name', $b['lab'])->lab_id,
                'requestor_name' => $b['requestor'],
                'contact_number' => $b['contact'],
                'purpose'        => $b['purpose'],
                'booking_date'   => $b['date']->format('Y-m-d'),
                'start_time'     => $b['start'],
                'end_time'       => $b['end'],
                'booking_status' => $b['status'],
                'created_at'     => Carbon::now()->subDays(2),
            ]);
        }

        // ─── 10. Guest logs + lab utilization (a few entries) ─────────
        // Student-based logs are not seeded — students live in the CSV import.
        $guests = [
            ['name' => 'Dr. Rodriguez',  'contact' => '+63 917 555 1234', 'purpose' => 'Equipment vendor inspection', 'lab' => 'Lab 1', 'time_in' => Carbon::now()->subHours(2), 'time_out' => null],
            ['name' => 'Atty. Castillo', 'contact' => '+63 918 555 5678', 'purpose' => 'Accreditation review',        'lab' => 'Lab 2', 'time_in' => Carbon::now()->subHours(4), 'time_out' => Carbon::now()->subHours(3)],
            ['name' => 'Anna Mendoza',   'contact' => '+63 919 555 9012', 'purpose' => 'Research observation',        'lab' => 'Lab 3', 'time_in' => Carbon::now()->subDay(),    'time_out' => Carbon::now()->subDay()->addHours(2)],
        ];
        foreach ($guests as $g) {
            $guestLog = GuestLog::create([
                'guest_name'     => $g['name'],
                'contact_number' => $g['contact'],
                'purpose'        => $g['purpose'],
            ]);

            LabUtilizationLog::create([
                'guest_log_id' => $guestLog->guest_log_id,
                'purpose'      => $g['purpose'],
                'lab_id'       => $labs->firstWhere('lab_name', $g['lab'])->lab_id,
                'log_date'     => $g['time_in']->toDateString(),
                'time_in'      => $g['time_in'],
                'time_out'     => $g['time_out'],
            ]);
        }
    }
}
