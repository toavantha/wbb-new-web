@extends('layouts.admin')

@section('title', 'Add Employee')
@section('page_title', 'Add Employee Profile')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Employee Details</h2>
            <a href="{{ route('admin.employees.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @foreach([
                'English' => ['name' => 'name', 'designation' => 'designation'],
                'Sinhala' => ['name' => 'name_si', 'designation' => 'designation_si'],
                'Tamil' => ['name' => 'name_ta', 'designation' => 'designation_ta'],
            ] as $language => $fields)
                <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
                    <legend class="px-2 text-xs font-bold uppercase tracking-wider text-wbb-800">{{ $language }} Content</legend>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach($fields as $type => $field)
                            @php($label = $type === 'name' ? 'Full Name' : 'Designation')
                            <div>
                                <label for="{{ $field }}" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $label }} *</label>
                                <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ old($field) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                                @error($field)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="mobile" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Mobile Number</label>
                    <input type="tel" name="mobile" id="mobile" value="{{ old('mobile') }}" inputmode="numeric" pattern="0[0-9]{9}" minlength="10" maxlength="10" title="{{ __('site.contact.phone_format_hint') }}" autocomplete="tel" data-digit-limit="10" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('mobile')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="image" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Employee Photo</label>
                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP; maximum 4 MB.</p>
                @error('image')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-wbb-600">
                <label for="is_active" class="cursor-pointer text-xs font-semibold text-slate-700">Publish profile in the public employee directory</label>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.employees.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">Add Employee</button>
            </div>
        </form>
    </div>
</div>
@endsection
