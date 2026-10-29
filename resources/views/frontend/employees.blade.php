@extends('layouts.frontend')

@section('title', __('site.titles.employees'))

@section('content')
<section class="bg-gradient-to-r from-slate-950 via-wbb-900 to-slate-950 py-12 lg:py-14 text-white border-b border-slate-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30 mb-3">{{ __('site.employees.people') }}</span>
        <h1 class="mt-1 text-3xl font-extrabold sm:text-4xl">{{ __('site.employees.heading') }}</h1>
        <p class="mt-2 max-w-2xl text-sm sm:text-base leading-relaxed text-slate-200">
            {{ __('site.employees.summary') }}
        </p>
    </div>
</section>

<section class="bg-slate-50 py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if($employees->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-xs">
                <i data-lucide="users" class="mx-auto h-10 w-10 text-slate-400"></i>
                <h2 class="mt-4 text-lg font-bold text-slate-900">{{ __('site.employees.coming_soon') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('site.employees.empty') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($employees as $employee)
                    <article class="flex items-center gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                        @if($employee->image_path)
                            <img
                                src="{{ asset('storage/' . $employee->image_path) }}"
                                alt="{{ $employee->localizedName() }}"
                                class="h-24 w-24 shrink-0 rounded-full border-2 border-amber-400 object-cover"
                            >
                        @else
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full border-2 border-amber-400 bg-wbb-900 text-3xl font-bold text-amber-400">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($employee->localizedName(), 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-slate-900">{{ $employee->localizedName() }}</h2>
                            <p class="mt-0.5 text-sm font-semibold text-wbb-700">{{ $employee->localizedDesignation() }}</p>
                            <div class="mt-3 space-y-1.5 text-xs text-slate-600">
                                @if($employee->mobile)
                                    <a href="tel:{{ $employee->mobile }}" class="flex items-center gap-2 hover:text-wbb-700">
                                        <i data-lucide="phone" class="h-3.5 w-3.5 shrink-0"></i>
                                        <span>{{ $employee->mobile }}</span>
                                    </a>
                                @endif
                                @if($employee->email)
                                    <a href="mailto:{{ $employee->email }}" class="flex items-center gap-2 break-all hover:text-wbb-700">
                                        <i data-lucide="mail" class="h-3.5 w-3.5 shrink-0"></i>
                                        <span>{{ $employee->email }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($employees->hasPages())
                <div class="mt-8">{{ $employees->links() }}</div>
            @endif
        @endif
    </div>
</section>
@endsection
