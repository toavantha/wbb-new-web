<form action="{{ $action }}" method="POST" class="space-y-5">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <p class="text-xs text-slate-500">Enter each message in English, Sinhala, and Tamil. All three translations are required.</p>

    @foreach(['en' => 'English', 'si' => 'Sinhala', 'ta' => 'Tamil'] as $locale => $language)
        <fieldset class="space-y-2 rounded-xl border border-slate-200 p-4">
            <legend class="px-2 text-xs font-extrabold uppercase tracking-wider text-wbb-800">{{ $language }} message *</legend>
            <label for="message_{{ $locale }}" class="sr-only">{{ $language }} ticker message</label>
            <textarea name="message_{{ $locale }}" id="message_{{ $locale }}" rows="3" maxlength="500" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">{{ old('message_'.$locale, $notice?->getAttribute('message_'.$locale)) }}</textarea>
            @error('message_'.$locale)
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </fieldset>
    @endforeach

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Display order *</label>
            <input type="number" name="sort_order" id="sort_order" min="0" max="999999" value="{{ old('sort_order', $notice?->sort_order ?? 0) }}" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('sort_order')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 self-end pb-3 text-xs font-semibold text-slate-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $notice?->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-wbb-600">
            <span>Active and visible on the homepage</span>
        </label>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
        <a href="{{ route('admin.important-information.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">
            {{ $method === 'PUT' ? 'Save Changes' : 'Add Notice' }}
        </button>
    </div>
</form>
