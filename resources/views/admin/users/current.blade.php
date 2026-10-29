@extends('layouts.admin')

@section('title', 'Current CMS Users')
@section('page_title', 'Current CMS Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-slate-500">Manage CMS accounts, including account details, roles, access status, and most recent successful login.</p>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 self-start rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
            <i data-lucide="user-check" class="h-4 w-4"></i>
            <span>Pending Registrations</span>
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-4 py-4">Email</th>
                        <th class="px-4 py-4">Role</th>
                        <th class="px-4 py-4">Access</th>
                        <th class="px-4 py-4">Registered</th>
                        <th class="px-6 py-4">Last Login</th>
                        <th class="px-6 py-4 text-right">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($currentUsers as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-4 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold capitalize text-blue-800">{{ $user->role }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $user->is_approved ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }}">
                                    {{ $user->is_approved ? 'Approved' : 'Not approved' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('Y-m-d H:i') }}
                                @elseif(auth()->id() === $user->id)
                                    Current session; login time unavailable
                                @else
                                    Never logged in
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-slate-300 px-3 py-2 font-bold text-slate-700 transition hover:bg-slate-50">Edit</a>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-confirm="Delete this CMS user account? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 font-bold text-rose-700 transition hover:bg-rose-50">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">There are no CMS user accounts.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($currentUsers->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $currentUsers->links() }}</div>
        @endif
    </div>
</div>
@endsection
