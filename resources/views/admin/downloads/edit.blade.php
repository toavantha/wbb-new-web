@extends('layouts.admin')

@section('title', 'Edit Download Item')
@section('page_title', 'Edit Download / Form')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Edit: {{ $download->title }}</h2>
            <a href="{{ route('admin.downloads.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.downloads.update', $download->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $download->title) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Item Type *</label>
                    <select name="category" id="category" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        @foreach(['Application Form', 'Handbook', 'Guideline'] as $cat)
                            <option value="{{ $cat }}" {{ (old('category', $download->category) === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="document_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Form / Ref Number</label>
                    <input type="text" name="document_number" id="document_number" value="{{ old('document_number', $download->document_number) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('document_number')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="language" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Language *</label>
                    <select name="language" id="language" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        @foreach(['Sinhala', 'Tamil', 'English', 'Trilingual'] as $lang)
                            <option value="{{ $lang }}" {{ (old('language', $download->language) === $lang) ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                    @error('language')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document File (PDF or DOCX)</label>
                    @if($download->file_path || $download->file_url)
                        <div class="mb-2">
                            <a href="{{ $download->file_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 hover:text-emerald-800 underline">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Current document: {{ $download->file_path ? basename($download->file_path) : 'Attached link' }} {{ $download->file_size ? "({$download->file_size})" : '' }}</span>
                            </a>
                        </div>
                    @endif
                    <input type="file" name="file" id="file" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2.5 file:text-xs file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-500">Allowed formats: PDF, DOCX (Max: 20MB). Leave empty to keep existing file.</p>
                    @error('file')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Release Date *</label>
                    <input type="date" name="published_date" id="published_date" value="{{ old('published_date', $download->published_date ? $download->published_date->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('published_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Form Purpose / Guidelines</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description', $download->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $download->is_active ? 'checked' : '' }} class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">Active and Visible in Public Downloads</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.downloads.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white text-xs font-bold shadow-md">Update Download Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
