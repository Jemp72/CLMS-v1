@extends('layouts.app')

@section('title', 'Instructors')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-baseline gap-3">
                <h1 class="text-primary text-2xl">Instructors</h1>
                <span class="text-sm text-muted">
                    <span class="font-semibold text-primary">{{ $instructors->count() }}</span>
                    {{ Str::plural('instructor', $instructors->count()) }}
                </span>
            </div>
            <p class="text-muted text-sm mt-1">Manage instructors assignable to class schedules</p>
        </div>
        <a href="{{ route('instructors.create') }}"
           class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm text-sm font-medium whitespace-nowrap">
            <x-icon name="plus" class="w-4 h-4" />
            Add Instructor
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="px-4 py-3 rounded-lg bg-success/10 border border-success/30 text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-black/10 shadow-sm overflow-hidden">

        @if ($instructors->isEmpty())
            <div class="text-center py-16">
                <x-icon name="user" class="w-12 h-12 text-muted mx-auto mb-3 opacity-30" />
                <p class="text-muted text-sm">No instructors yet</p>
                <a href="{{ route('instructors.create') }}" class="inline-block mt-2 text-xs text-primary hover:underline">
                    Add the first one →
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-surface border-b border-black/10">
                    <tr class="text-left text-xs font-semibold text-muted uppercase tracking-wide">
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3 text-right">Classes</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @foreach ($instructors as $instructor)
                        <tr class="hover:bg-surface transition-colors">
                            <td class="px-6 py-4 text-[#2c2c2c] font-medium">
                                {{ $instructor->last_name }}, {{ $instructor->first_name }}{{ $instructor->middle_name ? ' ' . substr($instructor->middle_name, 0, 1) . '.' : '' }}{{ $instructor->suffix ? ', ' . $instructor->suffix : '' }}
                            </td>
                            <td class="px-6 py-4 text-[#2c2c2c]">
                                {{ $instructor->email ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-block px-2 py-0.5 bg-primary/10 text-primary rounded text-xs font-semibold">
                                    {{ $instructor->schedules()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('instructors.destroy', $instructor->instructor_id) }}"
                                      onsubmit="return confirm('Remove this instructor?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
