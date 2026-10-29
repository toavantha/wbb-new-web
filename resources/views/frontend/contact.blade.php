@extends('layouts.frontend')

@section('title', __('site.titles.contact'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.contact.connect') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.contact.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.contact.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-14 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Contact Info & Map Column -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Contact Details Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">{{ __('site.contact.headquarters') }}</h2>

                    <div class="space-y-4 text-xs text-slate-700">
                        <!-- Location -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-wbb-800 flex items-center justify-center shrink-0">
                                <i data-lucide="map-pin" class="w-4 h-4 text-wbb-700"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 text-sm block">{{ __('site.contact.premises') }}</strong>
                                <span class="whitespace-pre-line">{{ $contactInformation->address }}</span>
                            </div>
                        </div>

                        <!-- Hotline -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <i data-lucide="phone-call" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 text-sm block">{{ __('site.contact.hotline') }}</strong>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactInformation->hotline) }}" class="text-base font-extrabold text-amber-600 hover:underline">{{ $contactInformation->hotline }}</a>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $contactInformation->localizedHotlineHours() }}</p>
                            </div>
                        </div>

                        <!-- General Line -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-600"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 text-sm block">{{ __('site.contact.admin_line') }}</strong>
                                <span>{{ $contactInformation->office_phone }}</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div>
                                <strong class="text-slate-900 text-sm block">{{ __('site.contact.email') }}</strong>
                                <a href="mailto:{{ $contactInformation->email_primary }}" class="text-wbb-700 font-semibold hover:underline block">{{ $contactInformation->email_primary }}</a>
                                <a href="mailto:{{ $contactInformation->email_secondary }}" class="text-slate-500 hover:underline block text-[11px]">{{ $contactInformation->email_secondary }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Maps location -->
                <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-xs h-64">
                    <div
                        id="contact-office-map"
                        class="h-full w-full"
                        data-latitude="{{ $contactInformation->latitude }}"
                        data-longitude="{{ $contactInformation->longitude }}"
                        data-google-maps-api-key="{{ config('services.google_maps.key') }}"
                        role="application"
                        aria-label="Headquarters office location on Google Maps"
                    ></div>
                </div>
                <p id="contact-office-map-status" class="text-xs text-amber-700" role="status" aria-live="polite">
                    @unless(config('services.google_maps.key'))
                        Google Maps is unavailable right now. Open the location in Google Maps using the link below.
                    @endunless
                </p>
                @if($contactInformation->google_maps_url)
                    <a href="{{ $contactInformation->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-semibold text-wbb-700 hover:underline">
                        <i data-lucide="map-pin" class="h-4 w-4" aria-hidden="true"></i>
                        Open this location in Google Maps
                    </a>
                @else
                    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ $contactInformation->latitude }},{{ $contactInformation->longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-semibold text-wbb-700 hover:underline">
                        <i data-lucide="map-pin" class="h-4 w-4" aria-hidden="true"></i>
                        Open this location in Google Maps
                    </a>
                @endif
            </div>

            <!-- Inquiry Submission Form Column -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ __('site.contact.inquiry') }}</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ __('site.contact.form_intro') }}
                    </p>
                </div>

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('site.contact.name') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="e.g. Sunil Wickramasinghe" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('site.contact.email_address') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="e.g. sunil@gmail.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('site.contact.phone') }}
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" placeholder="e.g. 0712345678" inputmode="numeric" pattern="0[0-9]{9}" minlength="10" maxlength="10" title="{{ __('site.contact.phone_format_hint') }}" autocomplete="tel" data-digit-limit="10" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                            @error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                {{ __('site.contact.subject') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required placeholder="{{ __('site.contact.subject_placeholder') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            {{ __('site.contact.message') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="5" required placeholder="{{ __('site.contact.message_placeholder') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">{{ old('message') }}</textarea>
                    </div>

                    @if ($turnstileEnabled)
                        <div>
                            <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                            @error('cf-turnstile-response')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    @else
                        <p class="text-sm text-rose-600">{{ __('site.contact.captcha_unavailable') }}</p>
                    @endif

                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-wbb-800 hover:bg-wbb-700 text-white font-bold py-3 px-8 rounded-xl text-xs sm:text-sm shadow-md transition">
                        <i data-lucide="send" class="w-4 h-4 text-amber-400"></i>
                        <span>{{ __('site.contact.submit') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@if ($turnstileEnabled)
    @push('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif
