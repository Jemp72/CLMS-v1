@extends('layouts.app')

@section('title', 'Add Instructor')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-primary text-2xl mb-1">Add Instructor</h1>
            <p class="text-muted text-sm">Register an instructor who can be assigned to class schedules</p>
        </div>
        <a href="{{ route('instructors.index') }}"
           class="flex items-center gap-2 px-4 py-2 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm text-muted">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back to Instructors
        </a>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm p-6">

        <form method="POST" action="{{ route('instructors.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        First Name <span class="text-primary">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full px-4 py-3 border @error('first_name') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Last Name <span class="text-primary">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full px-4 py-3 border @error('last_name') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="middle_name" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Middle Name
                    </label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                           class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                </div>

                <div>
                    <label for="suffix" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                        Suffix
                    </label>
                    <input type="text" id="suffix" name="suffix" value="{{ old('suffix') }}" placeholder="Jr., III"
                           class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="w-full px-4 py-3 border @error('email') border-red-400 @else border-black/10 @enderror rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact_number" class="block text-sm font-medium text-[#2c2c2c] mb-2">
                    Contact Number
                </label>
                <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                       class="w-full px-4 py-3 border border-black/10 rounded-lg bg-surface text-sm
                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('instructors.index') }}"
                   class="px-6 py-3 border border-black/10 rounded-lg hover:bg-surface transition-colors text-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium">
                    Add Instructor
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
