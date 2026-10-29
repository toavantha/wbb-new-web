@extends('layouts.admin')

@section('title', 'Add Welfare Scheme')
@section('page_title', 'Add Welfare Scheme')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Welfare Scheme Details</h2>
            <a href="{{ route('admin.welfare-schemes.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        @include('admin.welfare-schemes.form', ['nextSortOrder' => $nextSortOrder])
    </div>
</div>
@endsection
