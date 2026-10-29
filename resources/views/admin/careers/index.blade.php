@extends('layouts.admin')

@section('title', 'Manage Careers & Vacancies')
@section('page_title', 'Job Vacancies & Opportunities')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Post open job advertisements, qualifications, closing dates, and TOR documents.</p>
        <a href="{{ route('admin.careers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
            <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
            <span>Add Vacancy</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">Job Title</th>
                        <th class="py-4 px-4">Department</th>
                        <th class="py-4 px-4">Type</th>
                        <th class="py-4 px-4">Location</th>
                        <th class="py-4 px-4">Closing Date</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($careers as $job)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $job->title }}</div>
                                @if($job->salary_scale)
                                    <div class="text-[11px] text-amber-700 font-medium">{{ $job->salary_scale }}</div>
                                @endif
                                @if($uploaders->has($job->id))
                                    <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                        Documents uploaded by {{ $uploaders->get($job->id) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-700">
                                {{ $job->department }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-wbb-800 uppercase">
                                    {{ $job->employment_type }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-500">
                                {{ $job->location }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap font-mono text-slate-500">
                                {{ $job->closing_date ? $job->closing_date->format('Y-m-d') : 'Open' }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $job->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $job->is_active ? 'Active' : 'Closed' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.careers.edit', $job->id) }}" class="p-1.5 text-slate-400 hover:text-wbb-700 rounded transition" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.careers.destroy', $job->id) }}" method="POST" data-confirm="Delete this vacancy?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">No vacancies posted.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($careers->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $careers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
