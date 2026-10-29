@extends('layouts.frontend')

@section('title', __('site.titles.media'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.media.public_information') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.media.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.media.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Category Filter -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs mb-8 flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-2">{{ __('site.media.filter') }}</span>
            @foreach($categories as $cat)
                <a href="{{ route('media', ['category' => $cat]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($category === $cat || (!$category && $cat === 'All')) ? 'bg-wbb-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($news as $item)
                <article class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="h-48 bg-slate-200 overflow-hidden relative">
                            <img src="{{ $item->localizedMainImageUrl() ?? 'http://wbb.gov.lk/assets/img/sliderimgone.jpg' }}" alt="{{ $item->localized('title') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span class="absolute top-3 left-3 bg-wbb-900/90 text-amber-300 text-[10px] font-bold px-2.5 py-1 rounded-md backdrop-blur-xs">
                                {{ $item->category }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="text-[11px] text-slate-400 font-medium mb-1.5 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <span>{{ $item->published_date ? $item->published_date->format('M d, Y') : __('site.media.recent') }}</span>
                            </div>
                            <h3 class="font-bold text-base text-slate-900 mb-2 line-clamp-2 group-hover:text-wbb-700 transition">
                                <a href="{{ route('media.detail', $item->slug) }}">{{ $item->localized('title') }}</a>
                            </h3>
                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $item->localized('excerpt') }}
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <a href="{{ route('media.detail', $item->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-wbb-700 hover:text-wbb-900">
                            <span>{{ __('site.media.read_full') }}</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-12 text-slate-500 text-sm">
                    {{ __('site.media.empty') }}
                </div>
            @endforelse
        </div>

        @if($news->hasPages())
            <div class="mt-8">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
