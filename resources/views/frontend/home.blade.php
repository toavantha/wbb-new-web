@extends('layouts.frontend')

@section('title', __('site.titles.home'))

@section('content')
    <!-- Hero Section -->
    <section class="relative text-white overflow-hidden py-16 lg:py-24">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-bg.jpg') }}" alt="" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-wbb-900/80"></div>
        </div>
        <!-- Subtle Background Elements -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Text Column -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold tracking-wide uppercase">
                        <i data-lucide="shield-check" class="w-4 h-4 text-amber-400"></i>
                        <span>{{ __('site.home.portal_badge') }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        {{ __('site.home.board_name') }} <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-200">{{ __('site.home.ministry') }}</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        {{ __('site.home.intro') }}
                    </p>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="https://eservices.wbb.gov.lk" target="_blank"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-6 py-3.5 rounded-xl font-bold text-sm backdrop-blur-xs transition">
                            <span>{{ __('site.home.eservices') }}</span>
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>

                        <a href="https://iwms.wbb.gov.lk/household/public" target="_blank"
                            class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-6 py-3.5 rounded-xl font-bold text-sm backdrop-blur-xs transition">
                            <span>{{ __('site.home.household_register') }}</span>
                            <i data-lucide="external-link" class="w-4 h-4 text-amber-400"></i>
                        </a>

                        <a href="{{ route('downloads') }}"
                            class="inline-flex items-center gap-2 text-slate-300 hover:text-white px-3 py-2 text-xs font-semibold underline underline-offset-4 transition">
                            <i data-lucide="download" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>{{ __('site.home.application_forms') }}</span>
                        </a>
                    </div>

                    <!-- Verified Bank Logos Indicator -->
                    <div
                        class="pt-6 border-t border-slate-700/60 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-400">
                        <span class="font-medium text-slate-300">{{ __('site.home.direct_disbursements') }}</span>
                        <span class="font-bold text-white flex items-center gap-1.5"><i data-lucide="check"
                                class="w-4 h-4 text-emerald-400"></i> Bank of Ceylon</span>
                        <span class="font-bold text-white flex items-center gap-1.5"><i data-lucide="check"
                                class="w-4 h-4 text-emerald-400"></i> People's Bank</span>
                        <span class="font-bold text-white flex items-center gap-1.5"><i data-lucide="check"
                                class="w-4 h-4 text-emerald-400"></i> National Savings Bank</span>
                    </div>
                </div>

                <!-- External Gateways Card Column -->
                <div class="lg:col-span-5">
                    <div
                        class="bg-white/95 backdrop-blur-md rounded-2xl p-6 sm:p-8 shadow-2xl border border-white/40 text-slate-800 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div
                                class="w-10 h-10 rounded-xl bg-wbb-800 text-amber-400 flex items-center justify-center font-bold">
                                <i data-lucide="layers" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-base text-wbb-900 leading-tight">{{ __('site.home.gateways') }}</h2>
                                <p class="text-xs text-slate-500">{{ __('site.home.gateway_intro') }}</p>
                            </div>
                        </div>

                        <!-- Gateway 1: eServices -->
                        <a href="https://eservices.wbb.gov.lk" target="_blank"
                            class="block p-4 rounded-xl bg-amber-50 hover:bg-amber-100/80 border border-amber-200 transition group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center font-bold">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-amber-900">{{ __('site.home.eservices_title') }}</h3>
                                        <p class="text-[11px] text-slate-600">{{ __('site.home.appeals') }}</p>
                                    </div>
                                </div>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-amber-700"></i>
                            </div>
                        </a>

                        <!-- Gateway 2: IWMS Household Register -->
                        <a href="https://iwms.wbb.gov.lk/household/public" target="_blank"
                            class="block p-4 rounded-xl bg-blue-50 hover:bg-blue-100/80 border border-blue-200 transition group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-wbb-800 text-white flex items-center justify-center font-bold">
                                        <i data-lucide="database" class="w-4 h-4 text-amber-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-wbb-900">{{ __('site.home.social_registry') }}</h3>
                                        <p class="text-[11px] text-slate-600">{{ __('site.home.registry_lookup') }}
                                        </p>
                                    </div>
                                </div>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-wbb-700"></i>
                            </div>
                        </a>

                        <!-- Gateway 3: IWMS Officer Login -->
                        <a href="https://iwms.wbb.gov.lk/login" target="_blank"
                            class="block p-4 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold">
                                        <i data-lucide="key" class="w-4 h-4 text-slate-300"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900">{{ __('site.home.officer_login') }}</h3>
                                        <p class="text-[11px] text-slate-600">{{ __('site.home.staff_only') }}</p>
                                    </div>
                                </div>
                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-500"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- National Key Figures -->
    <section class="bg-white border-b border-slate-200 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-2xl sm:text-3xl font-extrabold text-wbb-800">{{ $stats['screened_families'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">{{ __('site.home.families') }}</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['active_beneficiaries'] }}
                    </div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">{{ __('site.home.households') }}</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-2xl sm:text-3xl font-extrabold text-wbb-800">{{ $stats['divisions'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">{{ __('site.home.divisions') }}</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['state_banks'] }}</div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">{{ __('site.home.bank_transfers') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Overview of Welfare Schemes Section -->
    <section class="py-16 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.home.national_schemes') }}</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.home.aswesuma_program') }}</h3>
                </div>
                <a href="{{ route('schemes') }}"
                    class="text-xs font-bold text-wbb-700 hover:text-wbb-900 flex items-center gap-1">
                    <span>{{ __('site.home.view_scheme_details') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="mb-6 flex items-start gap-3 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm font-semibold text-amber-950" role="note">
                <i data-lucide="info" class="mt-0.5 h-5 w-5 shrink-0 text-amber-700"></i>
                <p>{{ __('site.home.nic_mandatory_notice') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($homeSchemes as $scheme)
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                                    {{ $scheme['badge'] }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">{{ $scheme['duration'] }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mt-3 mb-1">{{ $scheme['name'] }}</h4>
                            <div class="text-2xl font-extrabold text-wbb-800 mb-3">{{ $scheme['allowance'] }}</div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $scheme['description'] }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>{{ __('site.home.direct_deposit') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest News & Announcements -->
    <section class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.home.press_releases') }}</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.home.latest_news') }}</h3>
                </div>
                <a href="{{ route('media') }}"
                    class="text-xs font-bold text-wbb-700 hover:text-wbb-900 flex items-center gap-1">
                    <span>{{ __('site.home.view_media') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($featuredNews as $news)
                    <article
                        class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col">
                        <div class="h-48 bg-slate-200 overflow-hidden relative">
                            <img src="{{ $news->localizedMainImageUrl() ?? 'http://wbb.gov.lk/assets/img/sliderimgone.jpg' }}"
                                alt="{{ $news->localized('title') }}"
                                class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            <span
                                class="absolute top-3 left-3 bg-wbb-900/90 text-amber-300 text-[10px] font-bold px-2.5 py-1 rounded-md backdrop-blur-xs">
                                {{ $news->category }}
                            </span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] text-slate-400 font-medium mb-1.5 flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span>{{ $news->published_date ? $news->published_date->format('M d, Y') : __('site.media.recent') }}</span>
                                </div>
                                <h4 class="font-bold text-base text-slate-900 mb-2 line-clamp-2 hover:text-wbb-700 transition">
                                    <a href="{{ route('media.detail', $news->slug) }}">{{ $news->localized('title') }}</a>
                                </h4>
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                                    {{ $news->localized('excerpt') }}
                                </p>
                            </div>
                            <a href="{{ route('media.detail', $news->slug) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-wbb-700 hover:text-wbb-900">
                                <span>{{ __('site.home.read_article') }}</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-500 text-sm">
                        {{ __('site.home.no_news') }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Latest Procurements & Tenders -->
    <section class="py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.procurements.tag') }}</span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ __('site.procurements.latest_procurements') }}</h3>
                </div>
                <a href="{{ route('procurements') }}"
                    class="text-xs font-bold text-wbb-700 hover:text-wbb-900 flex items-center gap-1">
                    <span>{{ __('site.procurements.view_all') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($latestProcurements as $procurement)
                    @php
                        $isExpired = $procurement->isExpired();
                        $isExtended = $procurement->isExtended();
                        $daysDiff = \Carbon\Carbon::today()->diffInDays($procurement->deadline_date, false);
                    @endphp
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        @if($procurement->category === 'Procurement Notice') bg-blue-100 text-wbb-800
                                        @elseif($procurement->category === 'Tender Document') bg-emerald-100 text-emerald-800
                                        @elseif($procurement->category === 'TOR') bg-purple-100 text-purple-800
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ $procurement->category }}
                                    </span>
                                    @if($procurement->reference_no)
                                        <span class="font-mono text-[10px] text-slate-500 font-semibold bg-white border border-slate-200 px-2 py-0.5 rounded">
                                            {{ $procurement->reference_no }}
                                        </span>
                                    @endif
                                </div>

                                @if($isExtended && ! $isExpired)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        {{ __('site.procurements.status_extended') }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-base font-bold text-slate-900 mb-2 leading-snug">
                                <a href="{{ route('procurements') }}" class="hover:text-wbb-700 transition">
                                    {{ $procurement->localizedTitle() }}
                                </a>
                            </h4>

                            @if($procurement->localizedDescription())
                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-4">
                                    {{ $procurement->localizedDescription() }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div class="space-y-0.5">
                                <div class="text-[11px] font-bold {{ $isExpired ? 'text-rose-600' : 'text-slate-700' }}">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>
                                    {{ __('site.procurements.deadline') }}: {{ $procurement->deadline_date ? $procurement->deadline_date->format('M d, Y') : '-' }}
                                </div>
                                @if(! $isExpired)
                                    <div class="text-[10px] font-semibold {{ $daysDiff <= 3 ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                                        @if($daysDiff === 0)
                                            {{ __('site.procurements.closing_today') }}
                                        @else
                                            {{ __('site.procurements.days_remaining', ['days' => $daysDiff]) }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('procurements.view', [$procurement, app()->getLocale()]) }}"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs shadow-xs transition">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('site.procurements.view_online') }}</span>
                                </a>

                                <a href="{{ route('procurements.download', [$procurement, app()->getLocale()]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs shadow-xs transition">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    <span>{{ __('site.procurements.download') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-10 text-slate-500 text-xs">
                        {{ __('site.procurements.no_current') }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Official Publications & Circulars -->
    <section class="py-14 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-xs font-bold text-wbb-700 uppercase tracking-widest">{{ __('site.home.repository') }}</span>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ __('site.home.official_docs') }}</h3>
                </div>
                <a href="{{ route('publications') }}"
                    class="text-xs font-bold text-wbb-700 hover:text-wbb-900 flex items-center gap-1">
                    <span>{{ __('site.home.full_repository') }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($latestCirculars as $pub)
                    @php
                        $docPath = $pub->file_path ?: parse_url($pub->getRawOriginal('file_url') ?? '', PHP_URL_PATH);
                        $isPdf = strtolower(pathinfo($docPath ?: '', PATHINFO_EXTENSION)) === 'pdf';
                        $viewUrl = $pub->file_path
                            ? ($isPdf ? route('publications.download', $pub->id) : route('publications.download', ['publication' => $pub->id, 'view' => 1]))
                            : ($pub->file_url ?? '#');
                        $downloadUrl = $pub->file_path
                            ? route('publications.download', ['publication' => $pub->id, 'download' => 1])
                            : ($pub->file_url ?? '#');
                    @endphp
                    <div
                        class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white flex items-start gap-4 transition shadow-xs">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-wbb-700 flex items-center justify-center shrink-0">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 uppercase">
                                    {{ $pub->category }}
                                </span>
                                @if($pub->document_number)
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $pub->document_number }}</span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $pub->title }}</h4>
                            <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $pub->description }}</p>
                        </div>
                        <div class="inline-flex items-center gap-1 shrink-0">
                            <a href="{{ $viewUrl }}" target="_blank" rel="noopener"
                                class="p-2 text-slate-500 hover:text-wbb-700 hover:bg-slate-100 rounded-lg transition"
                                title="{{ __('site.publications.view') }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ $downloadUrl }}"
                                class="p-2 text-slate-500 hover:text-wbb-700 hover:bg-slate-100 rounded-lg transition"
                                title="{{ __('site.home.download_document') }}">
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection