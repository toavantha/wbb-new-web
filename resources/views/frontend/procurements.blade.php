@extends('layouts.frontend')

@section('title', __('site.titles.procurements'))

@section('content')
<!-- Page Header Banner -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">
                {{ __('site.procurements.tag') }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                {{ __('site.procurements.heading') }}
            </h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.procurements.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Status Tabs: Current vs Expired -->
        <div class="flex items-center gap-3 border-b border-slate-200 pb-4 mb-8">
            <a href="{{ route('procurements', ['status' => 'current', 'category' => $category, 'search' => $search]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition {{ $status === 'current' ? 'bg-wbb-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i data-lucide="bell-ring" class="w-4 h-4 {{ $status === 'current' ? 'text-amber-400' : 'text-slate-400' }}"></i>
                <span>{{ __('site.procurements.current_notices') }}</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'current' ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-100 text-slate-700' }}">
                    {{ $currentCount }}
                </span>
            </a>

            <a href="{{ route('procurements', ['status' => 'expired', 'category' => $category, 'search' => $search]) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition {{ $status === 'expired' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <i data-lucide="archive" class="w-4 h-4 {{ $status === 'expired' ? 'text-amber-400' : 'text-slate-400' }}"></i>
                <span>{{ __('site.procurements.expired_notices') }}</span>
                <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'expired' ? 'bg-slate-700 text-white font-bold' : 'bg-slate-100 text-slate-700' }}">
                    {{ $expiredCount }}
                </span>
            </a>
        </div>

        <!-- Filters and Search Bar -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs mb-8">
            <form action="{{ route('procurements') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <input type="hidden" name="status" value="{{ $status }}">

                <!-- Category Filter Pills -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    @foreach($categories as $key => $label)
                        <a href="{{ route('procurements', ['status' => $status, 'category' => $key, 'search' => $search]) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($category === $key || (!$category && $key === 'All')) ? 'bg-wbb-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <!-- Keyword Search -->
                <div class="w-full md:w-72 relative">
                    <input type="hidden" name="category" value="{{ $category ?? 'All' }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('site.procurements.search_placeholder') }}" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                </div>
            </form>
        </div>

        <!-- Procurement Notices List -->
        @if($procurements->count() > 0)
            <div class="space-y-5">
                @foreach($procurements as $procurement)
                    @php
                        $isExpired = $procurement->isExpired();
                        $isExtended = $procurement->isExtended();
                        $daysDiff = \Carbon\Carbon::today()->diffInDays($procurement->deadline_date, false);
                    @endphp
                    <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:shadow-md transition">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        @if($procurement->category === 'Procurement Notice') bg-blue-100 text-wbb-800
                                        @elseif($procurement->category === 'Tender Document') bg-emerald-100 text-emerald-800
                                        @elseif($procurement->category === 'TOR') bg-purple-100 text-purple-800
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ $procurement->category }}
                                    </span>

                                    @if($procurement->reference_no)
                                        <span class="font-mono text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $procurement->reference_no }}
                                        </span>
                                    @endif

                                    @if($isExtended && ! $isExpired)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            <i data-lucide="clock-alert" class="w-3 h-3 text-amber-600"></i>
                                            {{ __('site.procurements.status_extended') }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug">
                                    {{ $procurement->localizedTitle() }}
                                </h2>

                                @if($procurement->localizedDescription())
                                    <p class="text-xs text-slate-600 leading-relaxed max-w-4xl">
                                        {{ $procurement->localizedDescription() }}
                                    </p>
                                @endif
                            </div>

                            <!-- Deadline Information Card -->
                            <div class="lg:text-right shrink-0 bg-slate-50 lg:bg-transparent p-3 lg:p-0 rounded-xl">
                                @if($isExpired)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i data-lucide="calendar-x" class="w-3.5 h-3.5 text-rose-500"></i>
                                        <span>{{ __('site.procurements.closed') }}: {{ $procurement->deadline_date->format('M d, Y') }}</span>
                                    </div>
                                @else
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold {{ $daysDiff <= 3 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                            <span>
                                                @if($isExtended)
                                                    {{ __('site.procurements.extended_deadline') }}: {{ $procurement->deadline_date->format('M d, Y') }}
                                                @else
                                                    {{ __('site.procurements.deadline') }}: {{ $procurement->deadline_date->format('M d, Y') }}
                                                @endif
                                            </span>
                                        </div>

                                        @if($isExtended && $procurement->original_deadline_date)
                                            <div class="text-[10px] text-slate-400">
                                                {{ __('site.procurements.original_deadline') }}: <span class="line-through">{{ $procurement->original_deadline_date->format('M d, Y') }}</span>
                                            </div>
                                        @endif

                                        <div class="text-[11px] font-semibold {{ $daysDiff <= 3 ? 'text-amber-700 font-bold' : 'text-slate-500' }}">
                                            @if($daysDiff === 0)
                                                {{ __('site.procurements.closing_today') }}
                                            @else
                                                {{ __('site.procurements.days_remaining', ['days' => $daysDiff]) }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer Details & Download Actions -->
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex flex-wrap items-center gap-3 text-slate-500 text-[11px]">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ __('site.procurements.published_date') }}: {{ $procurement->published_date ? $procurement->published_date->format('M d, Y') : '-' }}
                                </span>

                                @php
                                    $hasEn = !empty($procurement->file_path);
                                    $hasSi = !empty($procurement->file_path_si);
                                    $hasTa = !empty($procurement->file_path_ta);
                                    $multiLang = ($hasEn + $hasSi + $hasTa) > 1;
                                @endphp

                                @if($multiLang)
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1 font-medium">
                                        <span>{{ __('site.procurements.available_languages') }}:</span>
                                        @if($hasEn)
                                            <a href="{{ route('procurements.view', [$procurement, 'en']) }}" target="_blank" rel="noopener" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px]" title="View English Document">EN</a>
                                        @endif
                                        @if($hasSi)
                                            <a href="{{ route('procurements.view', [$procurement, 'si']) }}" target="_blank" rel="noopener" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px]" title="View Sinhala Document">SI</a>
                                        @endif
                                        @if($hasTa)
                                            <a href="{{ route('procurements.view', [$procurement, 'ta']) }}" target="_blank" rel="noopener" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px]" title="View Tamil Document">TA</a>
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('procurements.view', [$procurement, app()->getLocale()]) }}"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs shadow-xs hover:shadow transition">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-600"></i>
                                    <span>{{ __('site.procurements.view_online') }}</span>
                                </a>

                                <a href="{{ route('procurements.download', [$procurement, app()->getLocale()]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs shadow-xs hover:shadow transition">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('site.procurements.download_document') }}</span>
                                    @if($procurement->localizedFileSize())
                                        <span class="text-[10px] text-amber-300 font-normal">({{ $procurement->localizedFileSize() }})</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $procurements->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-400 mx-auto mb-3"></i>
                <h2 class="text-lg font-bold text-slate-900">
                    {{ $status === 'current' ? __('site.procurements.no_current') : __('site.procurements.no_expired') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    {{ __('site.procurements.summary') }}
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
