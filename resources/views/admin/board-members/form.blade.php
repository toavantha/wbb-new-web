@php
    $isEditing = isset($boardMember);
    $action = $isEditing
        ? route('admin.board-members.update', $boardMember)
        : route('admin.board-members.store');
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach(['name' => 'English Name', 'name_si' => 'Sinhala Name', 'name_ta' => 'Tamil Name'] as $field => $label)
            <div>
                <label for="{{ $field }}" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $label }} *</label>
                <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $boardMember->{$field} ?? '') }}" required maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                @error($field)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        @endforeach

        <div>
            <label for="position" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Position *</label>
            <select name="position" id="position" required class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                <option value="chairman" {{ old('position', $boardMember->position ?? 'member') === 'chairman' ? 'selected' : '' }}>{{ __('site.about_people.chairman_title') }}</option>
                <option value="member" {{ old('position', $boardMember->position ?? 'member') === 'member' ? 'selected' : '' }}>{{ __('site.about_people.member_title') }}</option>
            </select>
            @error('position')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email', $boardMember->email ?? '') }}" maxlength="191" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="sort_order" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Display Order *</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $boardMember->sort_order ?? $nextSortOrder ?? 1) }}" required min="0" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
            @error('sort_order')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    @if($isEditing && $boardMember->imageUrl())
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-700">Current Photo</p>
            <img src="{{ $boardMember->imageUrl() }}" alt="{{ $boardMember->name }}" class="h-24 w-24 rounded-full border-2 border-amber-400 object-cover">
        </div>
    @endif

    <div>
        <label for="image" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">{{ $isEditing ? 'Replace Photo' : 'Photo' }}</label>
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm">
        <p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP; maximum 4 MB. Leave blank to {{ $isEditing ? 'keep the current photo' : 'use initials' }}.</p>
        @error('image')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2 pt-2">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $boardMember->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-wbb-600">
        <label for="is_active" class="cursor-pointer text-xs font-semibold text-slate-700">Publish on the public About Us page</label>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
        <a href="{{ route('admin.board-members.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-xl bg-wbb-800 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-wbb-700">{{ $isEditing ? 'Update Board Member' : 'Add Board Member' }}</button>
    </div>
</form>
