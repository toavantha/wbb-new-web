@extends('layouts.frontend')

@section('title', __('site.titles.careers'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.careers.opportunities') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.careers.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.careers.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50 min-h-[600px]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($careers->count() > 0)
            <div class="space-y-6">
                @foreach($careers as $job)
                    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-wbb-800 uppercase">
                                        {{ $job->employment_type }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $job->department }}
                                    </span>
                                </div>
                                <h2 class="text-xl font-bold text-slate-900">{{ $job->title }}</h2>
                                <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                                    <span><i data-lucide="map-pin" class="w-3.5 h-3.5 inline text-amber-600 mr-1"></i>{{ $job->location }}</span>
                                    @if($job->salary_scale)
                                        <span><i data-lucide="award" class="w-3.5 h-3.5 inline text-amber-600 mr-1"></i>{{ $job->salary_scale }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="sm:text-right shrink-0">
                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full uppercase block mb-1">
                                    {{ __('site.careers.deadline') }} {{ $job->closing_date ? $job->closing_date->format('M d, Y') : __('site.careers.open') }}
                                </span>
                            </div>
                        </div>

                        <!-- Job Description & Qualifications -->
                        <div class="py-4 space-y-4 text-xs text-slate-700 leading-relaxed">
                            <div>
                                <h3 class="font-bold text-slate-900 mb-1 text-xs uppercase tracking-wider">{{ __('site.careers.overview') }}</h3>
                                <p>{{ $job->description }}</p>
                            </div>

                            <div>
                                <h3 class="font-bold text-slate-900 mb-1 text-xs uppercase tracking-wider">{{ __('site.careers.qualifications') }}</h3>
                                <p>{{ $job->qualifications }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <p class="text-xs text-slate-500">
                                {{ __('site.careers.application') }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['tor' => __('site.careers.tor'), 'details' => __('site.careers.details'), 'application' => __('site.careers.application_document')] as $documentType => $label)
                                    <a href="{{ route('careers.documents.download', [$job, $documentType]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                        <span>{{ __('site.careers.download_document', ['document' => $label]) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                <i data-lucide="briefcase" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                <h2 class="text-lg font-bold text-slate-900">{{ __('site.careers.none') }}</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    {{ __('site.careers.none_text') }}
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
