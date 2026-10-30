@extends('layouts.admin')

@section('title', 'Edit Important Information')
@section('page_title', 'Edit Important Information')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Edit Ticker Notice</h2>
            <a href="{{ route('admin.important-information.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        @include('admin.important-notices._form', [
            'notice' => $notice,
            'action' => route('admin.important-information.update', $notice->id),
            'method' => 'PUT',
        ])
    </div>
</div>
@endsection
