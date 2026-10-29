@extends('layouts.admin')

@section('title', 'Manage Procurements & Tenders')
@section('page_title', 'Procurements, Tenders & TORs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs text-slate-500">Manage Procurement Notices, Tender Documents, Terms of Reference (TOR), and Other notices in English, Sinhala, and Tamil.</p>
        </div>
        <a href="{{ route('admin.procurements.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs shadow-xs transition shrink-0">
            <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
            <span>Add New Procurement</span>
        </a>
    </div>

    <!-- Stats & Filters Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.procurements.index', ['status' => 'all', 'category' => $category, 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'all' ? 'bg-wbb-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.procurements.index', ['status' => 'current', 'category' => $category, 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'current' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Current Notices ({{ $currentCount }})
                </a>
                <a href="{{ route('admin.procurements.index', ['status' => 'expired', 'category' => $category, 'search' => $search]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $status === 'expired' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Expired Notices ({{ $expiredCount }})
                </a>
            </div>

            <!-- Search Field -->
            <form action="{{ route('admin.procurements.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="category" value="{{ $category }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search notices, ref..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
            </form>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-100 text-xs">
            <span class="text-slate-400 font-semibold text-[11px] mr-1">Category:</span>
            @foreach(['All', 'Procurement Notice', 'Tender Document', 'TOR', 'Other'] as $cat)
                <a href="{{ route('admin.procurements.index', ['status' => $status, 'category' => $cat, 'search' => $search]) }}"
                   class="px-2.5 py-1 rounded text-[11px] font-medium transition {{ $category === $cat ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Procurements Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">Procurement / Notice</th>
                        <th class="py-4 px-4">Category</th>
                        <th class="py-4 px-4">Ref No</th>
                        <th class="py-4 px-4">Languages & Files</th>
                        <th class="py-4 px-4">Deadline Date</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($procurements as $item)
                        @php
                            $isExpired = $item->isExpired();
                            $isExtended = $item->isExtended();
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-6 max-w-sm">
                                <div class="font-bold text-slate-900 text-sm leading-snug">{{ $item->title }}</div>
                                @if($item->title_si || $item->title_ta)
                                    <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                        @if($item->title_si) <span class="font-medium text-slate-600">[SI]: {{ $item->title_si }}</span> @endif
                                        @if($item->title_ta) <span class="font-medium text-slate-600 ml-2">[TA]: {{ $item->title_ta }}</span> @endif
                                    </div>
                                @endif
                                @if($uploaders->has($item->id))
                                    <div class="mt-1">
                                        <span class="inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                            By {{ $uploaders->get($item->id) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    @if($item->category === 'Procurement Notice') bg-blue-100 text-wbb-800
                                    @elseif($item->category === 'Tender Document') bg-emerald-100 text-emerald-800
                                    @elseif($item->category === 'TOR') bg-purple-100 text-purple-800
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-mono text-slate-500 text-[11px]">
                                {{ $item->reference_no ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex flex-wrap gap-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $item->file_path ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}" title="{{ $item->file_path ? 'English file uploaded' : 'No English file' }}">
                                        EN
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $item->file_path_si ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}" title="{{ $item->file_path_si ? 'Sinhala file uploaded' : 'Uses English fallback' }}">
                                        SI
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $item->file_path_ta ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-400' }}" title="{{ $item->file_path_ta ? 'Tamil file uploaded' : 'Uses English fallback' }}">
                                        TA
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Published: {{ $item->published_date ? $item->published_date->format('Y-m-d') : '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $item->deadline_date ? $item->deadline_date->format('Y-m-d') : '-' }}</div>
                                @if($isExtended)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded mt-0.5">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        Extended
                                    </span>
                                    @if($item->original_deadline_date)
                                        <div class="text-[10px] text-slate-400 line-through">
                                            was {{ $item->original_deadline_date->format('Y-m-d') }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                @if(! $item->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Draft</span>
                                @elseif($isExpired)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Expired</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Current</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <!-- View Online Button -->
                                    @if($item->file_path || $item->file_path_si || $item->file_path_ta)
                                        <a href="{{ route('procurements.view', [$item, 'en']) }}"
                                           target="_blank" rel="noopener"
                                           class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
                                           title="View Document Online">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    <!-- Quick Extend Deadline Button -->
                                    <button type="button"
                                            data-open-extend-modal
                                            data-procurement-id="{{ $item->id }}"
                                            data-title="{{ $item->title }}"
                                            data-deadline="{{ $item->deadline_date ? $item->deadline_date->format('Y-m-d') : '' }}"
                                            class="p-1.5 rounded-lg text-amber-600 hover:text-amber-800 hover:bg-amber-50 transition"
                                            title="Extend Deadline">
                                        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.procurements.edit', $item->id) }}"
                                       class="p-1.5 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition"
                                       title="Edit Procurement">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.procurements.destroy', $item->id) }}"
                                          method="POST"
                                          data-confirm="Are you sure you want to delete this procurement notice?"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No procurement notices found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $procurements->links() }}
        </div>
    </div>
</div>

<!-- Quick Extend Deadline Modal -->
<div id="extend-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="calendar-plus" class="w-4 h-4 text-amber-600"></i>
                <span>Extend Notice Deadline</span>
            </h3>
            <button type="button" data-close-extend-modal class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="extend-form" action="" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <p id="extend-notice-title" class="text-xs font-semibold text-slate-700 mb-2"></p>
                <div class="text-[11px] text-slate-500 mb-3">
                    Current Deadline: <span id="extend-current-deadline" class="font-bold text-slate-800"></span>
                </div>
            </div>

            <div>
                <label for="new_deadline_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    New Extended Deadline *
                </label>
                <input type="date" name="new_deadline_date" id="new_deadline_date" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                <p class="text-[11px] text-slate-400 mt-1">
                    Extending the deadline will move this notice up according to the new date and make it active if previously expired.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" data-close-extend-modal class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-xs">
                    Confirm Extension
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
