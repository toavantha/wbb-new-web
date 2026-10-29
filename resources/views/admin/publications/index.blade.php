@extends('layouts.admin')

@section('title', 'Manage Publications & Circulars')
@section('page_title', 'Publications, Circulars & Acts')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Upload and organize official Acts, Gazettes, and WBB Circulars.</p>
        <a href="{{ route('admin.publications.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs transition">
            <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
            <span>Add New Document</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-4 px-6">Document Title</th>
                        <th class="py-4 px-4">Category</th>
                        <th class="py-4 px-4">Ref Number</th>
                        <th class="py-4 px-4">Language</th>
                        <th class="py-4 px-4">Published Date</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($publications as $doc)
                        <tr class="hover:bg-slate-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-sm">{{ $doc->title }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-md mt-0.5">
                                    @if($doc->file_path)
                                        <span class="inline-flex items-center gap-1 text-slate-500 font-mono">
                                            <i data-lucide="file-text" class="w-3 h-3 text-slate-400"></i>
                                            {{ basename($doc->file_path) }}
                                            @if($doc->file_size)
                                                <span class="text-slate-400 font-sans">({{ $doc->file_size }})</span>
                                            @endif
                                        </span>
                                    @else
                                        {{ $doc->file_url }}
                                    @endif
                                </div>
                                @if($uploaders->has($doc->id))
                                    <span class="mt-1 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                        Uploaded by {{ $uploaders->get($doc->id) }}
                                    </span>
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
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-semibold">
                                    {{ $doc->language }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-slate-500">
                                {{ $doc->published_date ? $doc->published_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $doc->file_url ?? '#' }}" target="_blank" class="p-1.5 text-slate-400 hover:text-wbb-700 rounded transition" title="Preview">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.publications.edit', $doc->id) }}" class="p-1.5 text-slate-400 hover:text-wbb-700 rounded transition" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.publications.destroy', $doc->id) }}" method="POST" data-confirm="Delete this publication?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded transition" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">No publications added yet.</td>
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
@endsection
