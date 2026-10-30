@extends('layouts.admin')

@section('title', 'Important Information')
@section('page_title', 'Important Information Ticker')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <p class="text-xs text-slate-500">Manage the trilingual notices displayed below the homepage hero.</p>
        <a href="{{ route('admin.important-information.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-wbb-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-wbb-700">
            <i data-lucide="plus" class="h-4 w-4 text-amber-400"></i>
            <span>Add Notice</span>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">English Message</th>
                        <th class="px-4 py-4">Sinhala / Tamil</th>
                        <th class="px-4 py-4">Display Order</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notices as $notice)
                        <tr class="hover:bg-slate-50">
                            <td class="max-w-md px-6 py-4">
                                <p class="line-clamp-2 font-semibold text-slate-900">{{ $notice->message_en }}</p>
                            </td>
                            <td class="max-w-md px-4 py-4">
                                <p class="line-clamp-1">{{ $notice->message_si }}</p>
                                <p class="mt-1 line-clamp-1 text-slate-400">{{ $notice->message_ta }}</p>
                            </td>
                            <td class="px-4 py-4">{{ $notice->sort_order }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $notice->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $notice->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.important-information.edit', $notice->id) }}" class="rounded p-1.5 text-slate-500 transition hover:text-wbb-700" title="Edit notice">
                                        <i data-lucide="edit-3" class="h-4 w-4"></i>
                                    </a>
                                    <form action="{{ route('admin.important-information.destroy', $notice->id) }}" method="POST" data-confirm="Delete this important notice?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1.5 text-slate-500 transition hover:text-rose-600" title="Delete notice">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">No important notices created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notices->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $notices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
