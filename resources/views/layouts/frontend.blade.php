<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('site.titles.home'))</title>
    <meta name="description" content="{{ __('site.meta_description') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="{{ asset('images/welfare-benefits-board-vacancies.webp') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Sinhala:wght@400;600;700&family=Noto+Sans+Tamil:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen" data-conferbot-widget="6ae1a94e05846cdebc02a07a">

    <!-- Top Government Utility Bar -->
    <div class="bg-wbb-900 text-slate-200 text-xs border-b border-wbb-800">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-col sm:flex-row justify-between items-center gap-2">
            <!-- Official Affiliation -->
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    GOV.LK
                </span>
                <span class="text-slate-300">
                    {{ __('site.government_affiliation') }}
                </span>
            </div>

            <!-- Utility Controls & Hotline -->
            <div class="flex items-center gap-4">
                <a href="tel:1924"
                    class="flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-bold tracking-wide transition">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                    <span>{{ __('site.hotline') }}</span>
                </a>

                <span class="text-slate-600">|</span>

                <!-- Trilingual Switcher -->
                <div class="flex items-center gap-1 text-[11px] font-medium">
                    <span class="text-slate-400 mr-1">{{ __('site.language') }}:</span>
                    <a href="{{ route('locale.switch', 'en') }}"
                        class="px-2 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white' }}">English</a>
                    <a href="{{ route('locale.switch', 'si') }}"
                        class="px-2 py-0.5 rounded {{ app()->getLocale() === 'si' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white' }}">සිංහල</a>
                    <a href="{{ route('locale.switch', 'ta') }}"
                        class="px-2 py-0.5 rounded {{ app()->getLocale() === 'ta' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:text-white' }}">தமிழ்</a>
                </div>

                <span class="text-slate-600">|</span>

                <!-- Website CMS Admin Login -->
                <a href="{{ route('admin.login') }}"
                    class="flex items-center gap-1 text-slate-300 hover:text-white hover:underline transition">
                    <i data-lucide="lock" class="w-3 h-3"></i>
                    <span>{{ __('site.cms_login') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center group">
                    <img src="{{ asset('images/wbb-logo.png') }}"
                        alt="Welfare Benefits Board - සුබසාධක ප්‍රතිලාභ මණ්ඩලය - நலன்புரி நன்மைகள் சபை"
                        class="h-12 sm:h-14 w-auto object-contain">
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2 text-sm font-semibold text-slate-700">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.home') }}
                    </a>
                    <a href="{{ route('about') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('about') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.about') }}
                    </a>
                    <a href="{{ route('employees') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('employees') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.employees') }}
                    </a>
                    <a href="{{ route('schemes') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('schemes') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.schemes') }}
                    </a>
                    <a href="{{ route('publications') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('publications') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.publications') }}
                    </a>
                    <a href="{{ route('downloads') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('downloads') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.downloads') }}
                    </a>
                    <a href="{{ route('media') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('media*') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.media') }}
                    </a>
                    <a href="{{ route('careers') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('careers') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.careers') }}
                    </a>
                    <a href="{{ route('procurements') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('procurements*') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.procurements') }}
                    </a>
                    <a href="{{ route('contact.index') }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('contact.index') ? 'text-wbb-700 bg-blue-50 font-bold' : 'hover:text-wbb-700 hover:bg-slate-50' }}">
                        {{ __('site.nav.contact') }}
                    </a>
                    <a href="{{ route('search') }}"
                        aria-label="{{ __('site.nav.search') }}"
                        title="{{ __('site.nav.search') }}"
                        class="rounded-lg p-2 transition {{ request()->routeIs('search') ? 'bg-blue-50 text-wbb-700' : 'text-slate-600 hover:bg-slate-50 hover:text-wbb-700' }}">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </a>
                </nav>

                <!-- Mobile Hamburger Menu Toggle -->
                <button id="mobile-menu-btn"
                    class="lg:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-hidden">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-1">
            <a href="{{ route('home') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.home') }}</a>
            <a href="{{ route('about') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.about') }}</a>
            <a href="{{ route('employees') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.employees') }}</a>
            <a href="{{ route('schemes') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.schemes') }}</a>
            <a href="{{ route('publications') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.publications') }}</a>
            <a href="{{ route('downloads') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.downloads_forms') }}</a>
            <a href="{{ route('media') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.media_press') }}</a>
            <a href="{{ route('careers') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.careers') }}</a>
            <a href="{{ route('procurements') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.procurements') }}</a>
            <a href="{{ route('contact.index') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.contact_us') }}</a>
            <a href="{{ route('search') }}"
                class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-blue-50 hover:text-wbb-700">{{ __('site.nav.search') }}</a>

            <div class="pt-3 border-t border-slate-200 space-y-2">
                <a href="https://iwms.wbb.gov.lk/login" target="_blank"
                    class="block text-center font-bold text-xs py-2 px-3 rounded-lg bg-wbb-800 text-white">
                    {{ __('site.home.household_register') }} ({{ __('site.footer.external_portal') }}) &rarr;
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('admin.login') }}" class="text-xs text-wbb-700 font-semibold flex items-center gap-1">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    {{ __('site.cms_login') }}
                </a>
                <a href="tel:1924" class="text-xs text-amber-700 font-bold flex items-center gap-1">
                    <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                    {{ __('site.hotline') }}
                </a>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg flex items-start gap-3 shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                <div>
                    <div class="font-semibold text-sm">{{ __('site.success') }}</div>
                    <div class="text-xs sm:text-sm">{{ session('success') }}</div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-lg flex items-start gap-3 shadow-sm">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                <div>
                    <div class="font-semibold text-sm">{{ __('site.notice') }}</div>
                    <div class="text-xs sm:text-sm">{{ session('error') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Container -->
    <main class="grow">
        @yield('content')
    </main>

    <!-- Pre-Footer Hotline Banner -->
    <section class="bg-slate-100 py-8 border-t border-slate-200">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4 text-center md:text-left">
                <div
                    class="w-14 h-14 rounded-full bg-amber-100 border border-amber-400 flex items-center justify-center shrink-0 mx-auto md:mx-0">
                    <i data-lucide="headphones" class="w-7 h-7 text-amber-700"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-black">{{ __('site.footer.need_assistance') }}</h3>
                    <p class="text-black text-sm font-medium">{{ __('site.footer.hotline_hours') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="tel:1924"
                    class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 px-6 py-3 rounded-xl font-extrabold text-lg shadow-lg hover:shadow-xl transition">
                    <i data-lucide="phone-call" class="w-5 h-5"></i>
                    <span>{{ __('site.footer.dial') }}</span>
                </a>
                <a href="{{ route('contact.index') }}"
                    class="inline-flex items-center gap-2 bg-wbb-800 hover:bg-wbb-700 text-white px-5 py-3 rounded-xl font-semibold text-sm transition">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>{{ __('site.footer.send_message') }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Main Government Footer -->
    <footer class="bg-wbb-900 text-slate-400 text-sm border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <!-- Org Overview -->
                <div class="space-y-4">
                    <div>
                        <img src="{{ asset('images/wbb-logo.png') }}"
                            alt="                        {{ __('site.home.board_name') }}"
                            class="h-14 w-auto object-contain">
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">
                        {{ __('site.footer.established') }}
                    </p>
                    <div class="pt-2 text-xs text-slate-300">
                        <div class="font-semibold text-white mb-1">{{ __('site.footer.bank_partners') }}</div>
                        <div class="flex flex-wrap gap-2 text-[11px]">
                            <span class="bg-slate-800 px-2 py-1 rounded border border-slate-700">Bank of Ceylon</span>
                            <span class="bg-slate-800 px-2 py-1 rounded border border-slate-700">People's Bank</span>
                            <span class="bg-slate-800 px-2 py-1 rounded border border-slate-700">National Savings
                                Bank</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-slate-800 pb-2">
                        {{ __('site.footer.organization') }}
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('about') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.footer.about_board') }}</a></li>
                        <li><a href="{{ route('employees') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.nav.employees') }}</a></li>
                        <li><a href="{{ route('schemes') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.footer.welfare_tiers') }}</a></li>
                        <li><a href="{{ route('publications') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.footer.acts_gazettes') }}</a></li>
                        <li><a href="{{ route('downloads') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.footer.application_downloads') }}</a></li>
                        <li><a href="{{ route('careers') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.nav.careers') }}</a></li>
                        <li><a href="{{ route('procurements') }}"
                                class="hover:text-amber-400 transition flex items-center gap-1.5"><i
                                    data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                {{ __('site.nav.procurements') }}</a></li>
                    </ul>
                </div>

                <!-- External Welfare Portals -->
                <div class="space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-slate-800 pb-2">
                        {{ __('site.footer.external_portals') }}
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="https://eservices.wbb.gov.lk" target="_blank"
                                class="hover:text-amber-400 transition flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i data-lucide="external-link"
                                        class="w-3.5 h-3.5 text-amber-400"></i>
                                    {{ __('site.footer.external_appeals') }}</span>
                                <span class="text-[10px] text-slate-500">eservices.wbb.gov.lk</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://iwms.wbb.gov.lk/login" target="_blank"
                                class="hover:text-amber-400 transition flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i data-lucide="external-link"
                                        class="w-3.5 h-3.5 text-amber-400"></i>
                                    {{ __('site.footer.household_search') }}</span>
                                <span class="text-[10px] text-slate-500">iwms.wbb.gov.lk</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://iwms.wbb.gov.lk/login" target="_blank"
                                class="hover:text-amber-400 transition flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i data-lucide="external-link"
                                        class="w-3.5 h-3.5 text-amber-400"></i>
                                    {{ __('site.footer.officer_login') }}</span>
                                <span class="text-[10px] text-slate-500">iwms.wbb.gov.lk</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.treasury.gov.lk" target="_blank"
                                class="hover:text-amber-400 transition flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i data-lucide="external-link"
                                        class="w-3.5 h-3.5 text-amber-400"></i>
                                    {{ __('site.footer.finance_ministry') }}</span>
                                <span class="text-[10px] text-slate-500">treasury.gov.lk</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Headquarters Location & Contact -->
                <div class="space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider border-b border-slate-800 pb-2">
                        {{ __('site.footer.headquarters') }}
                    </h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="text-white">J.R. Jayawardena Centre</strong><br>
                                191 Srimath Anagarika Dharmapala Mawatha,<br>
                                Town Hall, Colombo 00700, Sri Lanka
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-amber-400 shrink-0"></i>
                            <span>{{ __('site.footer.general_phone') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-amber-400 shrink-0"></i>
                            <a href="mailto:info@iwms.wbb.gov.lk"
                                class="hover:text-white transition">info@iwms.wbb.gov.lk</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="external-link" class="w-4 h-4 text-amber-400 shrink-0"></i>
                            <a href="https://maps.google.com/?q=J+R+Jayawardena+Centre+Colombo" target="_blank"
                                class="text-amber-400 hover:underline">
                                {{ __('site.footer.view_map') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Compliance -->
            <div class="mt-12 pt-8 border-t border-slate-800/80 text-center text-xs text-slate-400">
                {{ __('site.footer.copyright', ['year' => date('Y')]) }}
                <p class="mt-2">Developed by WBB IT Team</p>
            </div>
            <!-- <p class="mt-4 text-right text-[10px] text-slate-400/20">Developer - Avantha Jayathunga</p> -->
        </div>
    </footer>

    @stack('scripts')
</body>

</html>