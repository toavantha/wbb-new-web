@extends('layouts.frontend')

@section('title', __('site.titles.about'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-16 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.about.profile') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.about.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-3 leading-relaxed">
                {{ __('site.about.summary') }}
            </p>
        </div>
    </div>
</div>

<!-- Legal Mandate & Historical Background -->
<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-5">
                <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.about.authority') }}</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ __('site.about.established') }}
                </h2>

                <p class="text-sm text-slate-700 leading-relaxed text-justify">
                    {{ __('site.about.history') }}
                </p>

                <p class="text-sm text-slate-700 leading-relaxed text-justify">
                    {{ __('site.about.mandate') }}
                </p>

                <!-- Key Stat Functions -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center gap-2 text-wbb-800 font-bold text-sm mb-1">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            <span>{{ __('site.about.criteria') }}</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ __('site.about.criteria_text') }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center gap-2 text-wbb-800 font-bold text-sm mb-1">
                            <i data-lucide="shield" class="w-4 h-4 text-wbb-600"></i>
                            <span>{{ __('site.about.appeals') }}</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ __('site.about.appeals_text') }}</p>
                    </div>
                </div>
            </div>

            <!-- Vision & Mission Cards -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Vision -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-slate-200">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold mb-4">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">{{ __('site.about.vision') }}</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-1 mb-2">{{ __('site.about.vision_title') }}</h3>
                    <p class="text-sm text-slate-700 leading-relaxed italic">
                        “{{ __('site.about.vision_text') }}”
                    </p>
                </div>

                <!-- Mission -->
                <div class="bg-slate-50 rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-wbb-700 flex items-center justify-center font-bold mb-4">
                        <i data-lucide="target" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.about.mission') }}</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-1 mb-2">{{ __('site.about.mission_title') }}</h3>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        {{ __('site.about.mission_text') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Board of Directors Section -->
<section class="py-16 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.about.leadership') }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.about.board') }}</h2>
            <p class="text-sm text-slate-600 mt-2">
                {{ __('site.about.board_appointment') }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($boardMembers as $member)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition text-center flex flex-col justify-between">
                    <div>
                        <div class="w-24 h-24 rounded-full mx-auto overflow-hidden bg-slate-100 border-2 border-amber-400 mb-4 p-0.5">
                            @if($member['image'])
                                <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-wbb-900 text-xl font-bold text-amber-400">
                                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="font-bold text-base text-slate-900">{{ $member['name'] }}</h3>
                        <div class="text-xs font-semibold text-wbb-700 mt-0.5">{{ $member['title'] }}</div>
                        <div class="text-[11px] text-slate-500 mb-4">{{ $member['organization'] }}</div>
                    </div>
                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 truncate">
                        @if($member['email'])
                            <i data-lucide="mail" class="w-3.5 h-3.5 inline mr-1 text-slate-500"></i>
                            {{ $member['email'] }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Ministry & Treasury Linkage -->
<section class="py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.about.government') }}</span>
            <h2 class="text-2xl font-extrabold text-slate-900 mt-1">{{ __('site.about.ministry') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @foreach($ministryLeadership as $leader)
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 flex items-center gap-5 hover:border-wbb-300 hover:shadow-xs transition">
                    @if($leader['image'])
                        <img src="{{ $leader['image'] }}" alt="{{ $leader['name'] }}" class="w-20 h-20 rounded-xl object-cover shrink-0 border border-slate-300 shadow-xs">
                    @else
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-wbb-900 text-xl font-bold text-amber-400">
                            {{ strtoupper(substr($leader['name'], 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="font-bold text-base text-slate-900 leading-snug">{{ $leader['name'] }}</h4>
                        <div class="text-xs font-semibold text-wbb-700 mt-1 leading-snug">{{ $leader['title'] }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $leader['ministry'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
