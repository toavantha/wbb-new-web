@extends('layouts.admin')

@section('title', 'Add Downloadable Item')
@section('page_title', 'Add Form / Guidebook')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Download Particulars</h2>
            <a href="{{ route('admin.downloads.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.downloads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. Aswesuma Phase II - Appeals Form" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Item Type *</label>
                    <select name="category" id="category" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        <option value="Application Form" {{ old('category') === 'Application Form' ? 'selected' : '' }}>Application Form</option>
                        <option value="Handbook" {{ old('category') === 'Handbook' ? 'selected' : '' }}>Handbook</option>
                        <option value="Guideline" {{ old('category') === 'Guideline' ? 'selected' : '' }}>Guideline</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="document_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Form / Ref Number</label>
                    <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}" placeholder="e.g. WBB/APP/2026/S" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('document_number')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="language" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Language *</label>
                    <select name="language" id="language" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        <option value="Sinhala" {{ old('language') === 'Sinhala' ? 'selected' : '' }}>Sinhala</option>
                        <option value="Tamil" {{ old('language') === 'Tamil' ? 'selected' : '' }}>Tamil</option>
                        <option value="English" {{ old('language') === 'English' ? 'selected' : '' }}>English</option>
                        <option value="Trilingual" {{ old('language') === 'Trilingual' ? 'selected' : '' }}>Trilingual</option>
                    </select>
                    @error('language')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document File (PDF or DOCX) *</label>
                    <input type="file" name="file" id="file" required accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2.5 file:text-xs file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-500">Allowed formats: PDF, DOCX (Max: 20MB)</p>
                    @error('file')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Release Date *</label>
                    <input type="date" name="published_date" id="published_date" value="{{ old('published_date', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('published_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Form Purpose / Guidelines</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">Active and Visible in Public Downloads</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.downloads.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white text-xs font-bold shadow-md">Add Download Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
