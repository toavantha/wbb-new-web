@extends('layouts.admin')

@section('title', 'Citizen Inquiry Details')
@section('page_title', 'Citizen Inquiry')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to Inquiries</a>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $message->status === 'New' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
            Status: {{ $message->status }}
        </span>
    </div>

    <!-- Message Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <span class="text-xs font-mono text-slate-400">Received {{ $message->created_at->format('F d, Y \a\t H:i') }}</span>
            <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $message->subject }}</h2>
            <div class="text-xs text-slate-600 mt-2 flex flex-wrap items-center gap-4">
                <span>From: <strong class="text-slate-900">{{ $message->name }}</strong></span>
                <span>Email: <a href="mailto:{{ $message->email }}" class="text-wbb-700 font-mono hover:underline">{{ $message->email }}</a></span>
                @if($message->phone)
                    <span>Phone: <a href="tel:{{ $message->phone }}" class="text-slate-700 font-mono hover:underline">{{ $message->phone }}</a></span>
                @endif
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Message Body</h3>
            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm leading-relaxed whitespace-pre-wrap">
                {{ $message->message }}
            </div>
        </div>

        <!-- Admin Response / Notes Form -->
        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Internal Status & Resolution Notes</h3>

            <form action="{{ route('admin.contacts.update', $message->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Update Status</label>
                    <select name="status" id="status" class="w-full sm:w-64 px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold">
                        @foreach(['New', 'In Progress', 'Responded', 'Resolved'] as $st)
                            <option value="{{ $st }}" {{ $message->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="admin_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Administrative Notes (Resolution Details)</label>
                    <textarea name="admin_notes" id="admin_notes" rows="3" placeholder="Add notes regarding email responses sent to citizen or division referral..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600">{{ old('admin_notes', $message->admin_notes) }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="mailto:{{ $message->email }}?subject=RE: {{ rawurlencode($message->subject) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        <i data-lucide="mail-reply" class="w-4 h-4 text-wbb-700"></i>
                        <span>Reply via Email Client</span>
                    </a>

                    <button type="submit" class="px-5 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs shadow-md transition">
                        Update Inquiry Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
