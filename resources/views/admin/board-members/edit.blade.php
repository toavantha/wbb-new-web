@extends('layouts.admin')

@section('title', 'Edit Board Member')
@section('page_title', 'Edit Board Member')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Edit: {{ $boardMember->name }}</h2>
            <a href="{{ route('admin.board-members.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        @include('admin.board-members.form', ['boardMember' => $boardMember])
    </div>
</div>
@endsection
