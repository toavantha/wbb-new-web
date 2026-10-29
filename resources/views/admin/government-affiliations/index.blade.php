@extends('layouts.admin')

@section('title', 'Government Affiliation')
@section('page_title', 'Government Affiliation')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-slate-500">Manage the Government Affiliation cards shown on the public About Us page.</p>
        <a href="{{ route('admin.government-affiliations.create') }}" class="inline-flex items-center gap-1.5 self-start rounded-xl bg-wbb-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-wbb-700">
            <i data-lucide="plus" class="h-4 w-4 text-amber-400"></i>
            <span>Add Affiliation</span>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Official</th>
                        <th class="px-4 py-4">Ministry</th>
                        <th class="px-4 py-4">Visibility</th>
                        <th class="px-4 py-4">Order</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($affiliations as $affiliation)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($affiliation->imageUrl())
                                        <img src="{{ $affiliation->imageUrl() }}" alt="" class="h-11 w-11 rounded-xl object-cover">
                                    @else
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-wbb-900 font-bold text-amber-400">
                                            {{ strtoupper(substr($affiliation->name_en, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $affiliation->name_en }}</div>
                                        <div class="mt-0.5 text-slate-500">{{ $affiliation->title_en }}</div>
                                        @if($affiliation->image_path && $photoUploaders->has($affiliation->id))
                                            <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                                Photo by {{ $photoUploaders->get($affiliation->id) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ $affiliation->ministry_en }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold {{ $affiliation->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $affiliation->is_active ? 'Published' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ $affiliation->sort_order }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.government-affiliations.edit', $affiliation) }}" class="rounded p-1.5 text-slate-400 transition hover:text-wbb-700" title="Edit affiliation">
                                        <i data-lucide="edit-3" class="h-4 w-4"></i>
                                    </a>
                                    <form action="{{ route('admin.government-affiliations.destroy', $affiliation) }}" method="POST" data-confirm="Delete this government affiliation?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1.5 text-slate-400 transition hover:text-rose-600" title="Delete affiliation">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">No government affiliations have been added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($affiliations->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $affiliations->links() }}</div>
        @endif
    </div>
</div>
@endsection
