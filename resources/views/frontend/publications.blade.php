@extends('layouts.frontend')

@section('title', __('site.titles.publications'))

@section('content')
<!-- Page Header -->
<div class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 text-white py-12 lg:py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.publications.documentation') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">{{ __('site.publications.heading') }}</h1>
            <p class="text-sm sm:text-base text-slate-200 mt-2 leading-relaxed">
                {{ __('site.publications.summary') }}
            </p>
        </div>
    </div>
</div>

<section class="py-12 bg-slate-50 min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search and Filter Bar -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs mb-8">
            <form action="{{ route('publications') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-center">
                <!-- Category Filter Pills -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    @foreach($categories as $key => $label)
                        <a href="{{ route('publications', ['category' => $key, 'search' => $search]) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ ($category === $key || (!$category && $key === 'All')) ? 'bg-wbb-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
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
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('site.publications.search') }}" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                </div>
            </form>
        </div>

        <!-- Publications Table / List -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-4 px-6">{{ __('site.publications.title') }}</th>
                            <th class="py-4 px-4">{{ __('site.publications.category') }}</th>
                            <th class="py-4 px-4">{{ __('site.publications.reference') }}</th>
                            <th class="py-4 px-4">{{ __('site.publications.language') }}</th>
                            <th class="py-4 px-4">{{ __('site.publications.date') }}</th>
                            <th class="py-4 px-6 text-right">{{ __('site.publications.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($publications as $doc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900 text-sm">{{ $doc->title }}</div>
                                    @if($doc->description)
                                        <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $doc->description }}</div>
                                    @endif
                                    @if($doc->file_size)
                                        <div class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $doc->file_size }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-wbb-800 uppercase">
                                        {{ $doc->category }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-500">
                                    {{ $doc->document_number ?? '-' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                        {{ $doc->language }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-slate-500 whitespace-nowrap">
                                    {{ $doc->published_date ? $doc->published_date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @php
                                        $documentPath = $doc->file_path ?: parse_url($doc->getRawOriginal('file_url') ?? '', PHP_URL_PATH);
                                        $isPdf = strtolower(pathinfo($documentPath ?: '', PATHINFO_EXTENSION)) === 'pdf';
                                        $canViewDocument = ! empty($documentPath);
                                        $viewUrl = $isPdf
                                            ? route('publications.download', $doc->id)
                                            : route('publications.download', ['publication' => $doc->id, 'view' => 1]);
                                        $downloadUrl = route('publications.download', ['publication' => $doc->id, 'download' => 1]);
                                    @endphp
                                    <div class="inline-flex items-center gap-2">
                                        @if($canViewDocument)
                                            <a href="{{ $viewUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                <span>{{ __('site.publications.view') }}</span>
                                            </a>
                                        @endif
                                        <a href="{{ $downloadUrl }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                            <span>{{ __('site.publications.download') }}</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-500 text-sm">
                                    {{ __('site.publications.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($publications->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $publications->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
