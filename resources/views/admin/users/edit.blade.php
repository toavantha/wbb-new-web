@extends('layouts.admin')

@section('title', 'Edit CMS User')
@section('page_title', 'Edit CMS User')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">User Account Details</h2>
            <p class="mt-1 text-xs text-slate-500">Update the account holder's name, email, role, and CMS access.</p>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="role" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Role</label>
                    <select name="role" id="role" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                        <option value="editor" @selected(old('role', $user->role) === 'editor')>Editor</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option>
                    </select>
                    @error('role')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="is_approved" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">CMS Access</label>
                    <select name="is_approved" id="is_approved" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                        <option value="1" @selected((string) old('is_approved', (int) $user->is_approved) === '1')>Approved</option>
                        <option value="0" @selected((string) old('is_approved', (int) $user->is_approved) === '0')>Not approved</option>
                    </select>
                    @error('is_approved')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                <a href="{{ route('admin.users.current') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">Save User</button>
            </div>
        </form>

        <div class="mt-8 border-t border-slate-100 pt-6">
            <h2 class="text-base font-bold text-slate-900">Set a Temporary Password</h2>
            <p class="mt-1 text-xs text-slate-500">The user will be required to change this password before continuing in the CMS.</p>

            <form action="{{ route('admin.users.temporary-password', $user) }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="temporary_password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Temporary Password</label>
                    <input type="password" name="temporary_password" id="temporary_password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('temporary_password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="temporary_password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Confirm Temporary Password</label>
                    <input type="password" name="temporary_password_confirmation" id="temporary_password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl border border-amber-300 bg-amber-50 px-6 py-2.5 text-xs font-bold text-amber-900 transition hover:bg-amber-100">Set Temporary Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
