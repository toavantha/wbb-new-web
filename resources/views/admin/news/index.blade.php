@extends('layouts.admin')

@section('title', 'Manage News & Press Releases')
@section('page_title', 'News & Media Releases')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Publish news, media releases, workshop updates, and press briefings.</p>
        <a href="{{ route('admin.news.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
            <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
            <span>Add News Article</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">Title & Excerpt</th>
                        <th class="py-4 px-4">Category</th>
                        <th class="py-4 px-4">Published Date</th>
                        <th class="py-4 px-4">Featured</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($news as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $item->title }}</div>
                                <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $item->excerpt }}</div>
                                @if($contentUploaders->has($item->id))
                                    <span class="mt-1 inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                        Content by {{ $contentUploaders->get($item->id) }}
                                    </span>
                                @endif
                                @if($photoUploaders->has($item->id))
                                    <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                        Photos by {{ $photoUploaders->get($item->id) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-slate-500">
                                {{ $item->published_date ? $item->published_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="py-4 px-4">
                                @if($item->is_featured)
                                    <span class="text-amber-600 font-bold flex items-center gap-1 text-[11px]">
                                        <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500"></i> Yes
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">No</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $item->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="p-1.5 text-slate-500 hover:text-wbb-700 rounded transition" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" data-confirm="Delete this news release?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 rounded transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">No articles created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($news->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
