@extends('layouts.admin')

@section('title', 'Create News Article')
@section('page_title', 'Create News & Press Release')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">New Article Details</h2>
            <a href="{{ route('admin.news.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
                    <legend class="px-2 text-sm font-extrabold uppercase tracking-wider text-wbb-800">{{ $language }} Content @if($locale === 'en') * @else (Optional) @endif</legend>

                    <div>
                        <label for="title{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Article Headline / Title {{ $locale === 'en' ? '*' : '' }}</label>
                        <input type="text" name="title{{ $locale === 'en' ? '' : '_'.$locale }}" id="title{{ $locale === 'en' ? '' : '_'.$locale }}" value="{{ old('title'.($locale === 'en' ? '' : '_'.$locale)) }}" @required($locale === 'en') maxlength="191" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600">
                        @error('title'.($locale === 'en' ? '' : '_'.$locale))
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Short Excerpt (Summary)</label>
                        <textarea name="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" id="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" rows="2" maxlength="500" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('excerpt'.($locale === 'en' ? '' : '_'.$locale)) }}</textarea>
                        @error('excerpt'.($locale === 'en' ? '' : '_'.$locale))
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Article Content {{ $locale === 'en' ? '*' : '' }}</label>
                        <textarea name="content{{ $locale === 'en' ? '' : '_'.$locale }}" id="content{{ $locale === 'en' ? '' : '_'.$locale }}" rows="8" @required($locale === 'en') class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('content'.($locale === 'en' ? '' : '_'.$locale)) }}</textarea>
                        @error('content'.($locale === 'en' ? '' : '_'.$locale))
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>
            @endforeach

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">News Category *</label>
                    <select name="category" id="category" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                        <option value="Press Release">Press Release</option>
                        <option value="Announcement">Announcement</option>
                        <option value="Notice">Notice</option>
                        <option value="Workshop">Workshop</option>
                    </select>
                </div>

                <div>
                    <label for="published_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Publication Date *</label>
                    <input type="date" name="published_date" id="published_date" value="{{ old('published_date', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>
            </div>

            <div class="space-y-4 rounded-xl border border-slate-200 p-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Article Images</h3>
                    <p class="mt-1 text-xs text-slate-500">The main image appears on the homepage and article cards. Sub-images appear in the article gallery. JPG, PNG, or WebP; maximum 4 MB per image.</p>
                </div>
                @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                    @php($suffix = $locale === 'en' ? '' : '_'.$locale)
                    <fieldset class="grid gap-4 rounded-lg border border-slate-200 p-3 sm:grid-cols-2">
                        <legend class="px-1 text-xs font-bold text-slate-700">{{ $language }} images</legend>
                        <div>
                            <label for="main_image{{ $suffix }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Main Image</label>
                            <input type="file" name="main_image{{ $suffix }}" id="main_image{{ $suffix }}" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                            @error('main_image'.$suffix)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sub_images{{ $suffix }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gallery Images</label>
                            <input type="file" name="sub_images{{ $suffix }}[]" id="sub_images{{ $suffix }}" accept="image/jpeg,image/png,image/webp" multiple class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                            <p class="mt-1 text-xs text-slate-500">Up to 20 images.</p>
                            @error('sub_images'.$suffix)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            @error('sub_images'.$suffix.'.*')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                    <span>Feature on Homepage Carousel</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" checked class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                    <span>Publish Immediately</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.news.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white text-xs font-bold shadow-md">Save & Publish</button>
            </div>
        </form>
    </div>
</div>
@endsection
