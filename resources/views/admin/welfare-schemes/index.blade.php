@extends('layouts.admin')

@section('title', 'Welfare Schemes')
@section('page_title', 'Welfare Schemes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-slate-500">Manage the benefit schemes and allowances shown on the public Welfare Schemes page.</p>
        <a href="{{ route('admin.welfare-schemes.create') }}" class="inline-flex items-center gap-1.5 self-start rounded-xl bg-wbb-800 px-4 py-2 text-xs font-bold text-white transition hover:bg-wbb-700">
            <i data-lucide="plus" class="h-4 w-4 text-amber-400"></i>
            <span>Add Welfare Scheme</span>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Scheme</th>
                        <th class="px-4 py-4">Monthly Allowance</th>
                        <th class="px-4 py-4">Visibility</th>
                        <th class="px-4 py-4">Order</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($welfareSchemes as $welfareScheme)
                        @php($schemeContent = $welfareScheme->localizedContent())
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $schemeContent['name'] }}</div>
                                <div class="mt-0.5 text-slate-500">{{ $schemeContent['badge'] }} · {{ $schemeContent['duration'] }}</div>
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-700">Rs. {{ number_format($welfareScheme->monthly_allowance) }} / month</td>
                            <td class="px-4 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold {{ $welfareScheme->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $welfareScheme->is_active ? 'Published' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ $welfareScheme->sort_order }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.welfare-schemes.edit', $welfareScheme) }}" class="rounded p-1.5 text-slate-400 transition hover:text-wbb-700" title="Edit welfare scheme">
                                        <i data-lucide="edit-3" class="h-4 w-4"></i>
                                    </a>
                                    <form action="{{ route('admin.welfare-schemes.destroy', $welfareScheme) }}" method="POST" data-confirm="Delete this welfare scheme?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded p-1.5 text-slate-400 transition hover:text-rose-600" title="Delete welfare scheme">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">No welfare schemes have been added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($welfareSchemes->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $welfareSchemes->links() }}</div>
        @endif
    </div>
</div>
@endsection
