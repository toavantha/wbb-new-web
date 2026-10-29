@extends('layouts.admin')

@section('title', 'Post Job Vacancy')
@section('page_title', 'Post New Career Opportunity')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Job Particulars</h2>
            <a href="{{ route('admin.careers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.careers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Job Designation / Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. Senior Database Administrator" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="department" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Department / Unit *</label>
                    <input type="text" name="department" id="department" value="{{ old('department', 'Integrated Welfare Management System (IWMS)') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>

                <div>
                    <label for="employment_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Employment Type *</label>
                    <select name="employment_type" id="employment_type" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        <option value="Full-Time">Full-Time (Permanent)</option>
                        <option value="Contract">Contract</option>
                        <option value="Consultant">Consultant</option>
                    </select>
                </div>

                <div>
                    <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Work Location *</label>
                    <input type="text" name="location" id="location" value="{{ old('location', 'Head Office, Colombo 07') }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="salary_scale" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Salary Scale / Category</label>
                    <input type="text" name="salary_scale" id="salary_scale" value="{{ old('salary_scale') }}" placeholder="e.g. Government Approved Scale (MM 1-1)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>

                <div>
                    <label for="closing_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Application Deadline *</label>
                    <input type="date" name="closing_date" id="closing_date" value="{{ old('closing_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>
            </div>

            <div class="space-y-4 rounded-xl border border-slate-200 p-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Vacancy Documents</h3>
                    <p class="mt-1 text-xs text-slate-500">Upload PDF or DOCX files (maximum 10 MB each). All documents are optional.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    @foreach(['tor' => 'TOR', 'details' => 'Details', 'application' => 'Application'] as $documentType => $label)
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">{{ $label }}</h4>
                            @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                                <div>
                                    <label for="{{ $documentType }}_{{ $locale }}_file" class="block text-xs font-medium text-slate-600 mb-1">{{ $language }}</label>
                                    <input type="file" name="{{ $documentType }}_{{ $locale }}_file" id="{{ $documentType }}_{{ $locale }}_file" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                                    @error($documentType.'_'.$locale.'_file')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Job Responsibilities & Scope *</label>
                <textarea name="description" id="description" rows="4" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="qualifications" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Required Educational Qualifications & Experience *</label>
                <textarea name="qualifications" id="qualifications" rows="4" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('qualifications') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700 cursor-pointer">Active and Visible on Careers Page</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.careers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white text-xs font-bold shadow-md">Post Vacancy</button>
            </div>
        </form>
    </div>
</div>
@endsection
