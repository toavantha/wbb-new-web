@extends('layouts.admin')

@section('title', 'User Registrations')
@section('page_title', 'Pending CMS Registrations')

@section('content')
<div class="space-y-6">
    <p class="text-xs text-slate-500">Review account requests. Approved users can sign in to the CMS; rejected requests are deleted.</p>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Applicant</th>
                        <th class="px-4 py-4">Requested</th>
                        <th class="px-6 py-4 text-right">Review</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingUsers as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="mt-0.5 text-slate-500">{{ $user->email }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-slate-500">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 font-bold text-white transition hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" data-confirm="Reject and delete this registration request?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 font-bold text-rose-700 transition hover:bg-rose-50">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-400">There are no pending registration requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingUsers->hasPages())
            <div class="border-t border-slate-200 p-4">{{ $pendingUsers->links() }}</div>
        @endif
    </div>
</div>
@endsection
