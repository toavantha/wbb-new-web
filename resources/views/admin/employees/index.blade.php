@extends('layouts.admin')

@section('title', 'Manage Employees')
@section('page_title', 'Employee Directory')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <p class="text-xs text-slate-500">Manage employee names, roles, photos, and public contact details.</p>
        <a href="{{ route('admin.employees.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-wbb-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-wbb-700">
            <i data-lucide="plus" class="h-4 w-4 text-amber-400"></i>
            <span>Add Employee</span>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-4 py-4">Contact</th>
                        <th class="px-4 py-4">Visibility</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($employee->image_path)
                                        <img src="{{ asset('storage/' . $employee->image_path) }}" alt="" class="h-11 w-11 rounded-full object-cover">
                                    @else
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-wbb-900 font-bold text-amber-400">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $employee->name }}</div>
                                        <div class="mt-0.5 text-slate-500">{{ $employee->designation }}</div>
                                        @if($employee->image_path && $photoUploaders->has($employee->id))
                                            <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                                Photo by {{ $photoUploaders->get($employee->id) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div>{{ $employee->mobile ?: 'No mobile listed' }}</div>
                                <div class="mt-0.5">{{ $employee->email ?: 'No email listed' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold {{ $employee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $employee->is_active ? 'Published' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="rounded p-1.5 text-slate-400 transition hover:text-wbb-700" title="Edit employee">
                                        <i data-lucide="edit-3" class="h-4 w-4"></i>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" data-confirm="Delete this employee profile?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1.5 text-slate-400 transition hover:text-rose-600" title="Delete employee">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">No employee profiles have been added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $employees->links() }}</div>
        @endif
    </div>
</div>
@endsection
