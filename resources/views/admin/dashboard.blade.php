@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('page_title', 'Welfare Benefits Board CMS Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">News & Media</span>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $counts['news'] }}</div>
                <a href="{{ route('admin.news.index') }}" class="text-xs text-wbb-700 font-semibold hover:underline mt-2 inline-block">Manage Releases &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-wbb-800 flex items-center justify-center font-bold">
                <i data-lucide="newspaper" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Circulars & Acts</span>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $counts['publications'] }}</div>
                <a href="{{ route('admin.publications.index') }}" class="text-xs text-wbb-700 font-semibold hover:underline mt-2 inline-block">Manage Documents &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Downloadable Forms</span>
                <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $counts['downloads'] }}</div>
                <a href="{{ route('admin.downloads.index') }}" class="text-xs text-wbb-700 font-semibold hover:underline mt-2 inline-block">Manage Handbooks &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                <i data-lucide="download" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citizen Inquiries</span>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ $counts['messages'] }} <span class="text-xs font-normal text-slate-400">new / {{ $counts['total_messages'] }}</span></div>
                <a href="{{ route('admin.contacts.index') }}" class="text-xs text-amber-700 font-semibold hover:underline mt-2 inline-block">View Messages &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div class="bg-gradient-to-r from-wbb-900 to-wbb-800 rounded-2xl p-6 text-white flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-base text-amber-400">Quick Content Management Actions</h3>
            <p class="text-xs text-slate-300">Publish directives, announcements, or downloadable documents for the public website.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.news.create') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Post News</span>
            </a>
            <a href="{{ route('admin.publications.create') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Circular</span>
            </a>
            <a href="{{ route('admin.downloads.create') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Form / Guide</span>
            </a>
            <a href="{{ route('admin.careers.create') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Post Vacancy</span>
            </a>
        </div>
    </div>

    <!-- Recent Data Grids -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent News Releases -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i data-lucide="newspaper" class="w-4 h-4 text-wbb-700"></i>
                    Recent News & Media Releases
                </h3>
                <a href="{{ route('admin.news.index') }}" class="text-xs text-wbb-700 hover:underline">View All &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($recentNews as $news)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                {{ $news->category }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $news->title }}</h4>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $news->published_date ? $news->published_date->format('M d, Y') : '' }}</div>
                        </div>
                        <a href="{{ route('admin.news.edit', $news->id) }}" class="p-1.5 text-slate-400 hover:text-wbb-700 rounded transition" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Publications & Circulars -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-700"></i>
                    Recent Circulars & Gazettes
                </h3>
                <a href="{{ route('admin.publications.index') }}" class="text-xs text-wbb-700 hover:underline">View All &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($recentPublications as $pub)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $pub->category }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 mt-1 truncate">{{ $pub->title }}</h4>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $pub->document_number ?? 'Published' }} &bull; {{ $pub->language }}</div>
                        </div>
                        <a href="{{ route('admin.publications.edit', $pub->id) }}" class="p-1.5 text-slate-400 hover:text-wbb-700 rounded transition" title="Edit">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Citizen Inquiries -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <i data-lucide="mail" class="w-4 h-4 text-amber-600"></i>
                Recent Citizen Inquiries Sent Via Website Contact Form
            </h3>
            <a href="{{ route('admin.contacts.index') }}" class="text-xs text-wbb-700 hover:underline">View All Messages &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Citizen Name</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Email / Phone</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentMessages as $msg)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $msg->name }}</td>
                            <td class="py-3 px-4 text-slate-700 truncate max-w-xs">{{ $msg->subject }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $msg->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $msg->status === 'New' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $msg->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.contacts.show', $msg->id) }}" class="text-xs font-bold text-wbb-700 hover:underline">View &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No recent messages.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
