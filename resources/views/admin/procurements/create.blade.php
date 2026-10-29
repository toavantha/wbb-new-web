@extends('layouts.admin')

@section('title', 'Add New Procurement')
@section('page_title', 'Create Procurement / Tender Notice')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">New Procurement Details</h2>
                <p class="text-xs text-slate-500 mt-0.5">Upload procurement notices, tender documents, terms of reference (TOR) or other notices.</p>
            </div>
            <a href="{{ route('admin.procurements.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.procurements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Notice Categorization & Key Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category *</label>
                    <select name="category" id="category" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600">
                        <option value="Procurement Notice" {{ old('category') === 'Procurement Notice' ? 'selected' : '' }}>Procurement Notice</option>
                        <option value="Tender Document" {{ old('category') === 'Tender Document' ? 'selected' : '' }}>Tender Document</option>
                        <option value="TOR" {{ old('category') === 'TOR' ? 'selected' : '' }}>TOR</option>
                        <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reference_no" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Reference No</label>
                    <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}" placeholder="e.g. WBB/PROC/2026/01" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600 font-mono">
                    @error('reference_no')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Published Date *</label>
                    <input type="date" name="published_date" id="published_date" value="{{ old('published_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600">
                    @error('published_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deadline Date *</label>
                    <input type="date" name="deadline_date" id="deadline_date" value="{{ old('deadline_date') }}" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-wbb-600">
                    @error('deadline_date')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- English Content (Default) -->
            <fieldset class="space-y-4 rounded-xl border-2 border-slate-200 p-5 bg-white">
                <legend class="px-2 text-xs font-black uppercase tracking-wider text-wbb-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>English Content (Default for all languages) *</span>
                </legend>
                <p class="text-[11px] text-slate-500">If Sinhala or Tamil content/files are not provided, this English content will be displayed on all language pages automatically.</p>

                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Title / Notice Name *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="191" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description / Overview</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_en" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document File (English / Default)</label>
                    <input type="file" name="file_en" id="file_en" accept=".pdf,.docx,.zip" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Accepted: PDF, DOCX, ZIP up to 20MB.</p>
                    @error('file_en')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <!-- Sinhala Content (Optional) -->
            <fieldset class="space-y-4 rounded-xl border border-slate-200 p-5 bg-white">
                <legend class="px-2 text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>Sinhala Content (සිංහල - Optional)</span>
                </legend>

                <div>
                    <label for="title_si" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Title in Sinhala</label>
                    <input type="text" name="title_si" id="title_si" value="{{ old('title_si') }}" maxlength="191" placeholder="ප්‍රසම්පාදන දැන්වීමේ මාතෘකාව..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('title_si')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description_si" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description in Sinhala</label>
                    <textarea name="description_si" id="description_si" rows="3" placeholder="විස්තරය..." class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description_si') }}</textarea>
                    @error('description_si')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_si" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document File (Sinhala Version)</label>
                    <input type="file" name="file_si" id="file_si" accept=".pdf,.docx,.zip" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">If not uploaded, the English document will be provided on the Sinhala page.</p>
                    @error('file_si')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <!-- Tamil Content (Optional) -->
            <fieldset class="space-y-4 rounded-xl border border-slate-200 p-5 bg-white">
                <legend class="px-2 text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    <span>Tamil Content (தமிழ் - Optional)</span>
                </legend>

                <div>
                    <label for="title_ta" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Title in Tamil</label>
                    <input type="text" name="title_ta" id="title_ta" value="{{ old('title_ta') }}" maxlength="191" placeholder="கொள்முதல் அறிவித்தல் தலைப்பு..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                    @error('title_ta')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description_ta" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Description in Tamil</label>
                    <textarea name="description_ta" id="description_ta" rows="3" placeholder="விவரம்..." class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description_ta') }}</textarea>
                    @error('description_ta')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_ta" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Document File (Tamil Version)</label>
                    <input type="file" name="file_ta" id="file_ta" accept=".pdf,.docx,.zip" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">If not uploaded, the English document will be provided on the Tamil page.</p>
                    @error('file_ta')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <!-- Publication Status -->
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 text-wbb-800 rounded border-slate-300 focus:ring-wbb-600">
                <label for="is_active" class="text-xs font-semibold text-slate-700">
                    Publish this notice immediately on the public website
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.procurements.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white font-bold text-xs shadow-xs transition">
                    Publish Procurement Notice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
