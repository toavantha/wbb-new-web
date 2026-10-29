@extends('layouts.frontend')

@section('title', __('site.titles.schemes'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-16 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.schemes.national_safety') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.schemes.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-3 leading-relaxed">
                {{ __('site.schemes.summary') }}
            </p>
        </div>
    </div>
</div>

<!-- Schemes Grid -->
<section class="py-14 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.schemes.tiers') }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.schemes.classification') }}</h2>
            <p class="text-sm text-slate-600 mt-2">
                {{ __('site.schemes.tier_summary') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schemes as $scheme)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 uppercase tracking-wider">
                                {{ $scheme['badge'] }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium">{{ $scheme['duration'] }}</span>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900 mb-1">{{ $scheme['name'] }}</h3>
                        <div class="text-2xl font-extrabold text-wbb-800 mb-3">{{ $scheme['allowance'] }}</div>

                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            {{ $scheme['description'] }}
                        </p>

                        <div class="space-y-1.5 pt-3 border-t border-slate-100">
                            <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">{{ __('site.schemes.key_criteria') }}</div>
                            @foreach($scheme['indicators'] as $ind)
                                <div class="text-xs text-slate-600 flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                    <span>{{ $ind }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">{{ __('site.schemes.monthly_credit') }}</span>
                        <a href="https://iwms.wbb.gov.lk/login" target="_blank" rel="noopener noreferrer" class="font-bold text-wbb-700 hover:text-wbb-900 flex items-center gap-1">
                            <span>IWMS Portal</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- The 22 Indicators Scoring Methodology -->
<section class="py-14 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.schemes.transparency') }}</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.schemes.indicators') }}</h2>
            <p class="text-sm text-slate-600 mt-2">
                {{ __('site.schemes.indicator_summary') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="home" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.housing') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.housing_text') }}</p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.livelihood') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.livelihood_text') }}</p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="heart-pulse" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.health') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.health_text') }}</p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="accessibility" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.disability') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.disability_text') }}</p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="grad-cap" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.demographics') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.demographics_text') }}</p>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-wbb-800 flex items-center justify-center font-bold mb-3">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-900 mb-1">{{ __('site.schemes.utilities') }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed">{{ __('site.schemes.utilities_text') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Direct Banking Partners Section -->
<section class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ __('site.schemes.banking') }}</h3>
        <p class="text-xs text-slate-500 max-w-xl mx-auto mb-6">
            {{ __('site.schemes.banking_text') }}
        </p>
        <div class="flex flex-wrap justify-center items-center gap-6 sm:gap-12">
            <div class="px-6 py-3 rounded-xl bg-white border border-slate-200 font-bold text-wbb-800 text-sm shadow-xs">
                Bank of Ceylon (BOC)
            </div>
            <div class="px-6 py-3 rounded-xl bg-white border border-slate-200 font-bold text-wbb-800 text-sm shadow-xs">
                People's Bank
            </div>
            <div class="px-6 py-3 rounded-xl bg-white border border-slate-200 font-bold text-wbb-800 text-sm shadow-xs">
                National Savings Bank (NSB)
            </div>
        </div>
    </div>
</section>
@endsection
