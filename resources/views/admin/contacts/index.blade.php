@extends('layouts.admin')

@section('title', 'Citizen Inquiries & Messages')
@section('page_title', 'Citizen Messages & Inquiries')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-xs text-slate-500">View public inquiries transmitted through the website contact form.</p>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.contacts.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ !$status ? 'bg-wbb-800 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">All</a>
            <a href="{{ route('admin.contacts.index', ['status' => 'New']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $status === 'New' ? 'bg-amber-500 text-slate-950' : 'bg-white text-slate-700 border border-slate-200' }}">New</a>
            <a href="{{ route('admin.contacts.index', ['status' => 'Responded']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $status === 'Responded' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">Responded</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">Sender Details</th>
                        <th class="py-4 px-4">Subject</th>
                        <th class="py-4 px-4">Received On</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $msg->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $msg->email }} &bull; {{ $msg->phone ?? 'No Phone' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800 truncate max-w-sm">{{ $msg->subject }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-sm mt-0.5">{{ $msg->message }}</div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-slate-500">
                                {{ $msg->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $msg->status === 'New' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $msg->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.contacts.show', $msg->id) }}" class="px-3 py-1 rounded-lg bg-wbb-800 text-white font-bold text-xs hover:bg-wbb-700 transition">
                                        Open
                                    </a>
                                    <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" data-confirm="Delete message?">
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
                            <td colspan="5" class="text-center py-12 text-slate-400">No citizen messages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
