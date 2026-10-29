@extends('layouts.admin')

@section('title', 'CMS Activity')
@section('page_title', 'CMS Activity History')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4">
        <p class="text-xs text-slate-500">News, document, and photo changes made by CMS users.</p>
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:flex-row sm:items-end sm:justify-between">
            <form action="{{ route('admin.activity.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label for="start_date" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-600">From</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('start_date')
                        <span class="mt-1 block text-[10px] text-rose-600">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-600">To</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end_date }}" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('end_date')
                        <span class="mt-1 block text-[10px] text-rose-600">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="rounded-lg bg-wbb-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-wbb-700">Filter</button>
                @if($start_date || $end_date)
                    <a href="{{ route('admin.activity.index') }}" class="px-2 py-2 text-xs font-semibold text-slate-600 hover:text-wbb-800">Clear</a>
                @endif
            </form>

            @can('manage-cms-users')
                <a href="{{ route('admin.activity.export', array_filter(['start_date' => $start_date, 'end_date' => $end_date])) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-800">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    <span>Download log (.txt)</span>
                </a>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">User</th>
                        <th class="py-4 px-4">Action</th>
                        <th class="py-4 px-4">Content</th>
                        <th class="py-4 px-4">Details</th>
                        <th class="py-4 px-6">Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activityLogs as $activity)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-6 font-semibold text-slate-800">{{ $activity->user?->name ?? $activity->actor_name }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-bold capitalize">{{ $activity->action }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800">{{ $activity->subject_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase">{{ $activity->subject_type }}</div>
                            </td>
                            <td class="py-4 px-4">{{ $activity->details ?? '-' }}</td>
                            <td class="py-4 px-6 whitespace-nowrap text-slate-500">{{ $activity->created_at->timezone(config('cms.timezone'))->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">No CMS activity has been recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activityLogs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
