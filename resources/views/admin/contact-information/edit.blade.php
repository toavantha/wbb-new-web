@extends('layouts.admin')

@section('title', 'Contact Information')
@section('page_title', 'Contact Information')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Edit Contact Page Details</h2>
            <p class="mt-1 text-xs text-slate-500">These details appear in the public Contact Us page.</p>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.contact-information.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="address" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Headquarters Address *</label>
                <textarea name="address" id="address" rows="4" required maxlength="2000" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">{{ old('address', $contactInformation->address) }}</textarea>
                @error('address')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">Use a new line for each address line.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="hotline" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Hotline *</label>
                    <input type="text" name="hotline" id="hotline" value="{{ old('hotline', $contactInformation->hotline) }}" required maxlength="50" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('hotline')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="office_phone" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">General Office Phone *</label>
                    <input type="text" name="office_phone" id="office_phone" value="{{ old('office_phone', $contactInformation->office_phone) }}" required maxlength="100" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('office_phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email_primary" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Primary Email *</label>
                    <input type="email" name="email_primary" id="email_primary" value="{{ old('email_primary', $contactInformation->email_primary) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('email_primary')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email_secondary" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Secondary Email *</label>
                    <input type="email" name="email_secondary" id="email_secondary" value="{{ old('email_secondary', $contactInformation->email_secondary) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error('email_secondary')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
                <legend class="px-2 text-xs font-bold uppercase tracking-wider text-wbb-800">Hotline Hours by Language</legend>
                <div class="grid grid-cols-1 gap-4">
                    @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                        @php($field = 'hotline_hours_'.$locale)
                        <div>
                            <label for="{{ $field }}" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $language }} *</label>
                            <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $contactInformation->{$field}) }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                            @error($field)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <div>
                <div>
                    <label for="google_maps_url" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Google Maps Location URL</label>
                    <input type="url" name="google_maps_url" id="google_maps_url" value="{{ old('google_maps_url', $contactInformation->google_maps_url) }}" maxlength="2048" placeholder="https://www.google.com/maps/place/Location/@6.9026888,79.9627975,18z/..." class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    <p class="mt-1 text-xs text-slate-500">Paste the full Google Maps location URL. It must include coordinates in the URL, such as @latitude,longitude,zoom.</p>
                    @error('google_maps_url')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end border-t border-slate-100 pt-4">
                <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">Save Contact Information</button>
            </div>
        </form>
    </div>
</div>
@endsection
