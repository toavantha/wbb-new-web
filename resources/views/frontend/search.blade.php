@extends('layouts.frontend')

@section('title', __('site.search.title'))

@section('content')
    <section class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 py-12 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">{{ __('site.search.title') }}</h1>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-200 sm:text-base">
                {{ __('site.search.description') }}
            </p>
        </div>
    </section>

    <section class="min-h-[500px] bg-slate-50 py-10 sm:py-12">
        <div class="mx-auto max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">
            <form action="{{ route('search') }}" method="GET"
                class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xs sm:flex-row sm:p-6">
                <label class="sr-only" for="global-search">{{ __('site.search.input_label') }}</label>
                <input
                    id="global-search"
                    name="q"
                    type="search"
                    value="{{ old('q', $query) }}"
                    maxlength="191"
                    placeholder="{{ __('site.search.placeholder') }}"
                    class="min-w-0 grow rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-wbb-600 focus:outline-hidden focus:ring-2 focus:ring-wbb-600"
                >
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-wbb-800 px-6 py-3 text-sm font-bold text-white transition hover:bg-wbb-700 focus:outline-hidden focus:ring-2 focus:ring-wbb-600 focus:ring-offset-2">
                    <i data-lucide="search" class="h-4 w-4"></i>
                    {{ __('site.search.submit') }}
                </button>
                @error('q')
                    <p class="text-sm font-medium text-rose-700 sm:basis-full">{{ $message }}</p>
                @enderror
            </form>

            @if($query !== '')
                <div aria-live="polite">
                    <h2 class="text-lg font-bold text-slate-900">
                        {{ __('site.search.results_for', ['query' => $query]) }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ __('site.search.results_count', ['count' => $results->total()]) }}
                    </p>
                </div>

                @if($results->count() > 0)
                    <div class="space-y-4">
                        @foreach($results as $result)
                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-wbb-800">
                                        {{ __('site.search.types.'.$result['type']) }}
                                    </span>
                                    @if($result['locale'])
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                            {{ __('site.search.languages.'.$result['locale']) }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="mt-3 text-lg font-bold text-slate-900">
                                    <a href="{{ $result['url'] }}" class="hover:text-wbb-700">
                                        {{ $result['title'] }}
                                    </a>
                                </h3>
                                @if($result['excerpt'] !== '')
                                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600">
                                        {{ $result['excerpt'] }}
                                    </p>
                                @endif
                                <a href="{{ $result['url'] }}"
                                    class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-wbb-700 hover:text-wbb-900">
                                    {{ __('site.search.open_result') }}
                                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    @if($results->hasPages())
                        <div>{{ $results->links() }}</div>
                    @endif
                @else
                    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-xs">
                        <i data-lucide="search-x" class="mx-auto h-10 w-10 text-slate-400"></i>
                        <p class="mt-4 text-sm text-slate-600">{{ __('site.search.no_results') }}</p>
                    </div>
                @endif
            @else
                <p class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-xs">
                    {{ __('site.search.enter_query') }}
                </p>
            @endif
        </div>
    </section>
@endsection
