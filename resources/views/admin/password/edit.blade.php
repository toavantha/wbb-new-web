@extends('layouts.admin')

@section('title', 'Change Password')
@section('page_title', 'Change Your Password')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Update Account Password</h2>
            <p class="mt-1 text-xs text-slate-500">
                @if(auth()->user()->must_change_password)
                    Your administrator set a temporary password. Change it now to continue using the CMS.
                @else
                    Confirm your current password before choosing a new one.
                @endif
            </p>
        </div>

        <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Current Password</label>
                <input type="password" name="current_password" id="current_password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                @error('current_password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">New Password</label>
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                <p class="mt-1 text-xs text-slate-500">Must be at least 8 characters and different from your current password.</p>
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-4">
                <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-wbb-700">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
