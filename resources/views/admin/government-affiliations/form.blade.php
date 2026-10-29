@php
    $isEditing = isset($governmentAffiliation);
    $action = $isEditing
        ? route('admin.government-affiliations.update', $governmentAffiliation)
        : route('admin.government-affiliations.store');
    $languages = [
        'en' => 'English',
        'si' => 'Sinhala',
        'ta' => 'Tamil',
    ];
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    @foreach($languages as $locale => $language)
        <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
            <legend class="px-2 text-xs font-bold uppercase tracking-wider text-wbb-800">{{ $language }} Content</legend>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach(['name' => 'Official Name', 'title' => 'Title', 'ministry' => 'Ministry'] as $field => $label)
                    @php($inputName = $field.'_'.$locale)
                    <div>
                        <label for="{{ $inputName }}" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $label }} *</label>
                        <input type="text" name="{{ $inputName }}" id="{{ $inputName }}" value="{{ old($inputName, $governmentAffiliation->{$inputName} ?? '') }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                        @error($inputName)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </fieldset>
    @endforeach

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Display Order *</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $governmentAffiliation->sort_order ?? $nextSortOrder ?? 1) }}" required min="0" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('sort_order')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            @if($isEditing && $governmentAffiliation->imageUrl())
                <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-700">Current Photo</p>
                <img src="{{ $governmentAffiliation->imageUrl() }}" alt="{{ $governmentAffiliation->name_en }}" class="h-20 w-20 rounded-xl border border-slate-300 object-cover">
            @endif
            <label for="image" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $isEditing ? 'Replace Photo' : 'Photo' }}</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP; maximum 4 MB. Leave blank to {{ $isEditing ? 'keep the current photo' : 'use initials' }}.</p>
            @error('image')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $governmentAffiliation->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-wbb-600">
        <label for="is_active" class="cursor-pointer text-xs font-semibold text-slate-700">Publish on the public About Us page</label>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
        <a href="{{ route('admin.government-affiliations.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">{{ $isEditing ? 'Update Affiliation' : 'Add Affiliation' }}</button>
    </div>
</form>
