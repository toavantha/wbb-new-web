@extends('layouts.frontend')

@section('title', __('site.titles.downloads'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.downloads.forms') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.downloads.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.downloads.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Language Filter Bar -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('site.downloads.filter') }}</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($languages as $lang)
                        <a href="{{ route('downloads', ['language' => $lang]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($language === $lang || (!$language && $lang === 'All')) ? 'bg-wbb-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ __('site.downloads.languages.' . strtolower($lang)) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="text-xs text-slate-500">
                {{ __('site.downloads.free') }}
            </div>
        </div>

        <!-- Downloads Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($downloads as $item)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 uppercase tracking-wider">
                                {{ $item->category }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                {{ $item->language }}
                            </span>
                        </div>

                        <h3 class="font-bold text-base text-slate-900 mb-2 leading-snug group-hover:text-wbb-700 transition">
                            {{ $item->title }}
                        </h3>

                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            {{ $item->description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-[11px] text-slate-400">
                            {{ $item->file_size ?? __('site.downloads.pdf') }} &bull; {{ $item->published_date ? $item->published_date->format('Y-m-d') : '' }}
                        </div>
                        <a href="{{ $item->file_url ?? '#' }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>{{ __('site.downloads.download_pdf') }}</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-slate-500 text-sm">
                    {{ __('site.downloads.empty') }}
                </div>
            @endforelse
        </div>

        @if($downloads->hasPages())
            <div class="mt-8">
                {{ $downloads->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
