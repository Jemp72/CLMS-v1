<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstructorRequest;
use App\Models\Instructor;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::orderBy('last_name')->orderBy('first_name')->get();

        return view('instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('instructors.create');
    }

    public function store(StoreInstructorRequest $request)
    {
        Instructor::create($request->validated());

        return redirect()
            ->route('instructors.index')
            ->with('success', 'Instructor has been added.');
    }

    public function destroy(int $id)
    {
        $instructor = Instructor::findOrFail($id);

        // Block deletion if the instructor is referenced by a class schedule.
        if ($instructor->schedules()->exists()) {
            return back()->with('error',
                'Cannot delete — this instructor is assigned to one or more class schedules.'
            );
        }

        $instructor->delete();

        return back()->with('success', 'Instructor removed.');
    }
}
