@extends('layouts.admin')

@section('title', 'Edit News Article')
@section('page_title', 'Edit News & Press Release')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Edit: {{ $newsItem->title }}</h2>
            <a href="{{ route('admin.news.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <form action="{{ route('admin.news.update', $newsItem->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
                    <legend class="px-2 text-sm font-extrabold uppercase tracking-wider text-wbb-800">{{ $language }} Content @if($locale === 'en') * @else (Optional) @endif</legend>

                    <div>
                        <label for="title{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Article Headline / Title {{ $locale === 'en' ? '*' : '' }}</label>
                        <input type="text" name="title{{ $locale === 'en' ? '' : '_'.$locale }}" id="title{{ $locale === 'en' ? '' : '_'.$locale }}" value="{{ old('title'.($locale === 'en' ? '' : '_'.$locale), $newsItem->{'title'.($locale === 'en' ? '' : '_'.$locale)}) }}" @required($locale === 'en') maxlength="191" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600">
                        @error('title'.($locale === 'en' ? '' : '_'.$locale))
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Short Excerpt (Summary)</label>
                        <textarea name="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" id="excerpt{{ $locale === 'en' ? '' : '_'.$locale }}" rows="2" maxlength="500" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('excerpt'.($locale === 'en' ? '' : '_'.$locale), $newsItem->{'excerpt'.($locale === 'en' ? '' : '_'.$locale)}) }}</textarea>
                        @error('excerpt'.($locale === 'en' ? '' : '_'.$locale))
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content{{ $locale === 'en' ? '' : '_'.$locale }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Article Content {{ $locale === 'en' ? '*' : '' }}</label>
                        <textarea name="content{{ $locale === 'en' ? '' : '_'.$locale }}" id="content{{ $locale === 'en' ? '' : '_'.$locale }}" rows="8" @required($locale === 'en') class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">{{ old('content'.($locale === 'en' ? '' : '_'.$locale), $newsItem->{'content'.($locale === 'en' ? '' : '_'.$locale)}) }}</textarea>
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
                        @foreach(['Press Release', 'Announcement', 'Notice', 'Workshop'] as $cat)
                            <option value="{{ $cat }}" {{ (old('category', $newsItem->category) === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="published_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Publication Date *</label>
                    <input type="date" name="published_date" id="published_date" value="{{ old('published_date', $newsItem->published_date ? $newsItem->published_date->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-wbb-600">
                </div>
            </div>

            <div class="space-y-4 rounded-xl border border-slate-200 p-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Article Images</h3>
                    <p class="mt-1 text-xs text-slate-500">The main image appears on the homepage and article cards. Sub-images appear in the article gallery. JPG, PNG, or WebP; maximum 4 MB per image.</p>
                </div>
                @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
                    @php
                        $suffix = $locale === 'en' ? '' : '_'.$locale;
                        $mainImagePathAttribute = 'main_image_path'.$suffix;
                        $currentMainImageUrl = $newsItem->localizedMainImageUrl($locale);
                        $localeImages = $newsItem->images->where('locale', $locale);
                    @endphp
                    <fieldset class="space-y-4 rounded-lg border border-slate-200 p-3">
                        <legend class="px-1 text-xs font-bold text-slate-700">{{ $language }} images</legend>
                        @if($currentMainImageUrl)
                            <div>
                                <p class="mb-2 text-xs font-bold text-slate-700 uppercase tracking-wider">Current Main Image</p>
                                <img src="{{ $currentMainImageUrl }}" alt="{{ $newsItem->title }}" class="h-32 w-48 rounded-lg border border-slate-200 object-cover">
                            </div>
                        @endif
                        <div>
                            <label for="main_image{{ $suffix }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Replace Main Image</label>
                            <input type="file" name="main_image{{ $suffix }}" id="main_image{{ $suffix }}" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                            <p class="mt-1 text-xs text-slate-500">Leave empty to keep the current image. If this language has no image, the public page uses English.</p>
                            @error('main_image'.$suffix)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if($localeImages->isNotEmpty())
                            <div>
                                <h4 class="mb-2 text-xs font-bold text-slate-700 uppercase tracking-wider">Current Gallery Images</h4>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    @foreach($localeImages as $image)
                                        <label class="space-y-2 rounded-lg border border-slate-200 p-2">
                                            <img src="{{ $image->url }}" alt="{{ $newsItem->title }}" class="h-24 w-full rounded object-cover">
                                            <span class="flex items-center gap-2 text-xs text-slate-600">
                                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded border-slate-300 text-rose-600">
                                                Remove image
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div>
                            <label for="sub_images{{ $suffix }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Add Gallery Images</label>
                            <input type="file" name="sub_images{{ $suffix }}[]" id="sub_images{{ $suffix }}" accept="image/jpeg,image/png,image/webp" multiple class="w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                            <p class="mt-1 text-xs text-slate-500">Existing images stay unless marked for removal. Up to 20 images per update.</p>
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
                    <input type="checkbox" name="is_featured" value="1" {{ $newsItem->is_featured ? 'checked' : '' }} class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                    <span>Feature on Homepage</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" {{ $newsItem->is_published ? 'checked' : '' }} class="w-4 h-4 text-wbb-600 rounded border-slate-300">
                    <span>Published</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.news.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-wbb-800 hover:bg-wbb-700 text-white text-xs font-bold shadow-md">Update Article</button>
            </div>
        </form>
    </div>
</div>
@endsection
