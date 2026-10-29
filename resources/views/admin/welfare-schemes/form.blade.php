@php
    $isEditing = isset($welfareScheme);
    $action = $isEditing
        ? route('admin.welfare-schemes.update', $welfareScheme)
        : route('admin.welfare-schemes.store');
    $languages = [
        'en' => 'English',
        'si' => 'Sinhala',
        'ta' => 'Tamil',
    ];
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="monthly_allowance" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Monthly Allowance (Rs.) *</label>
            <input type="number" name="monthly_allowance" id="monthly_allowance" value="{{ old('monthly_allowance', $welfareScheme->monthly_allowance ?? '') }}" required min="0" step="1" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('monthly_allowance')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="sort_order" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Display Order *</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $welfareScheme->sort_order ?? $nextSortOrder ?? 1) }}" required min="0" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('sort_order')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    @foreach($languages as $locale => $language)
        @php($localeContent = isset($welfareScheme) ? $welfareScheme->content[$locale] ?? [] : [])
        <fieldset class="space-y-4 rounded-xl border border-slate-200 p-4">
            <legend class="px-2 text-xs font-bold uppercase tracking-wider text-wbb-800">{{ $language }} Content</legend>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="content_{{ $locale }}_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Scheme Name *</label>
                    <input type="text" name="content[{{ $locale }}][name]" id="content_{{ $locale }}_name" value="{{ old("content.{$locale}.name", $localeContent['name'] ?? '') }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error("content.{$locale}.name")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="content_{{ $locale }}_badge" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Badge *</label>
                    <input type="text" name="content[{{ $locale }}][badge]" id="content_{{ $locale }}_badge" value="{{ old("content.{$locale}.badge", $localeContent['badge'] ?? '') }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error("content.{$locale}.badge")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="content_{{ $locale }}_duration" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Duration *</label>
                    <input type="text" name="content[{{ $locale }}][duration]" id="content_{{ $locale }}_duration" value="{{ old("content.{$locale}.duration", $localeContent['duration'] ?? '') }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    @error("content.{$locale}.duration")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="content_{{ $locale }}_description" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Description *</label>
                    <textarea name="content[{{ $locale }}][description]" id="content_{{ $locale }}_description" rows="3" required maxlength="2000" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">{{ old("content.{$locale}.description", $localeContent['description'] ?? '') }}</textarea>
                    @error("content.{$locale}.description")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="content_{{ $locale }}_indicators" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Key Criteria (one per line) *</label>
                    <textarea name="content[{{ $locale }}][indicators]" id="content_{{ $locale }}_indicators" rows="4" required maxlength="4000" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">{{ old("content.{$locale}.indicators", isset($localeContent['indicators']) ? implode("\n", $localeContent['indicators']) : '') }}</textarea>
                    @error("content.{$locale}.indicators")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </fieldset>
    @endforeach

    <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $welfareScheme->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-wbb-600">
        <label for="is_active" class="cursor-pointer text-xs font-semibold text-slate-700">Publish on the public Welfare Schemes page</label>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
        <a href="{{ route('admin.welfare-schemes.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">{{ $isEditing ? 'Update Welfare Scheme' : 'Add Welfare Scheme' }}</button>
    </div>
</form>
