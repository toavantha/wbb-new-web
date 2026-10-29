@extends('layouts.frontend')

@section('title', $article->localized('title') . ' | ' . __('site.home.board_name'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('media') }}" class="inline-flex items-center gap-1.5 text-xs text-amber-300 hover:text-amber-200 hover:underline mb-3 font-semibold">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>{{ __('site.media_detail.back') }}</span>
        </a>
        <div class="max-w-3xl">
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">
                {{ $article->category }}
            </span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mt-2 leading-tight tracking-tight">
                {{ $article->localized('title') }}
            </h1>
            <div class="text-xs text-slate-200 mt-3 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-amber-400"></i>
                <span>{{ __('site.media_detail.published') }} {{ $article->published_date ? $article->published_date->format('F d, Y') : __('site.media.recent') }}</span>
                <span>&bull;</span>
                <span>{{ __('site.media_detail.communications') }}</span>
            </div>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Article Body Column -->
            <div class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-10 border border-slate-200 shadow-xs space-y-6">
                @if($article->localizedMainImageUrl())
                    <div class="rounded-xl overflow-hidden border border-slate-200 max-h-[420px]">
                        <img src="{{ $article->localizedMainImageUrl() }}" alt="{{ $article->localized('title') }}" class="w-full h-full object-cover">
                    </div>
                @endif

                @if($article->localizedImages()->isNotEmpty())
                    <div class="space-y-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900">{{ __('site.media_detail.gallery') }}</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($article->localizedImages() as $image)
                                <figure class="overflow-hidden rounded-xl border border-slate-200">
                                    <img src="{{ $image->url }}" alt="{{ $article->localized('title') }}" loading="lazy" class="h-64 w-full object-cover">
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($article->localized('excerpt'))
                    <div class="p-4 rounded-xl bg-amber-50 border-l-4 border-amber-500 text-sm font-medium text-amber-950 leading-relaxed italic">
                        {{ $article->localized('excerpt') }}
                    </div>
                @endif

                <div class="prose max-w-none text-slate-700 text-sm leading-relaxed space-y-4 text-justify">
                    {!! nl2br(e($article->localized('content'))) !!}
                </div>

                <div class="pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs">
                    <div class="text-slate-500">
                        {{ __('site.media_detail.verified') }}
                    </div>
                    <a href="{{ route('media') }}" class="font-bold text-wbb-800 hover:underline">
                        &larr; {{ __('site.media_detail.archive') }}
                    </a>
                </div>
            </div>

            <!-- Recent News Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                    <h3 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-3">
                        {{ __('site.media_detail.recent') }}
                    </h3>
                    <div class="space-y-4">
                        @foreach($recentArticles as $recent)
                            <div class="group">
                                <span class="text-[10px] text-amber-600 font-bold block mb-1">
                                    {{ $recent->published_date ? $recent->published_date->format('M d, Y') : '' }}
                                </span>
                                <h4 class="font-bold text-xs text-slate-900 group-hover:text-wbb-700 leading-snug">
                                    <a href="{{ route('media.detail', $recent->slug) }}">{{ $recent->localized('title') }}</a>
                                </h4>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- External Quick Link Box -->
                <div class="bg-gradient-to-br from-wbb-900 to-wbb-800 text-white rounded-2xl p-6 space-y-3">
                    <div class="font-bold text-sm text-amber-400">{{ __('site.media_detail.looking_for') }}</div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ __('site.media_detail.portal_description') }}
                    </p>
                    <a href="https://eservices.wbb.gov.lk" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-950 bg-amber-500 hover:bg-amber-400 px-3 py-2 rounded-lg transition">
                        <span>{{ __('site.media_detail.launch') }}</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
