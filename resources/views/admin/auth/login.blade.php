<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrative Login | Welfare Benefits Board</title>

    <link rel="icon" type="image/webp" href="{{ asset('images/welfare-benefits-board-vacancies.webp') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-800 font-sans antialiased flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <img src="http://wbb.gov.lk/assets/img/gov.png" alt="Emblem" class="h-16 w-auto mx-auto mb-3">
            <h1 class="text-xl font-extrabold text-white">WELFARE BENEFITS BOARD</h1>
            <p class="text-xs text-amber-400 font-medium tracking-wider uppercase mt-1">Website Content Management System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Administrative Sign In</h2>
            <p class="text-xs text-slate-500 mb-6">Restricted to authorized WBB IT and Communications personnel.</p>

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 px-3 py-2 rounded-lg text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-3 py-2 rounded-lg text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Officer Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" name="password" id="password" required autocomplete="current-password" class="w-full pl-9 pr-16 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-wbb-600 focus:border-wbb-600 transition">
                        <button type="button" id="toggle-password-visibility" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-wbb-700 hover:text-wbb-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-wbb-600 rounded">
                            Show
                        </button>
                    </div>
                </div>

                <div class="flex justify-end text-xs pt-1">
                    <span class="text-slate-400">Authorized users only</span>
                </div>

                <button type="submit" class="w-full bg-wbb-800 hover:bg-wbb-700 text-white font-bold py-3 px-4 rounded-xl text-xs sm:text-sm shadow-md transition mt-2">
                    Access Content Management System
                </button>
            </form>

            <div class="mt-5 text-center text-xs text-slate-500">
                New to the CMS?
                <a href="{{ route('admin.register') }}" class="font-semibold text-wbb-700 hover:underline">Request an account</a>
            </div>

            <details class="mt-4 text-xs text-slate-500">
                <summary class="cursor-pointer text-center font-semibold text-wbb-700 hover:underline">Resend email verification</summary>
                <form action="{{ route('verification.send') }}" method="POST" class="mt-3 space-y-3">
                    @csrf
                    <label for="verification-email" class="block font-semibold text-slate-700">Registration email address</label>
                    <input type="email" name="email" id="verification-email" required autocomplete="email" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    <button type="submit" class="w-full rounded-xl border border-wbb-700 px-3 py-2 font-semibold text-wbb-700 hover:bg-blue-50">Send verification email</button>
                </form>
            </details>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-wbb-700 font-semibold flex items-center justify-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Return to Public Website</span>
                </a>
            </div>
        </div>

        <div class="text-center text-[11px] text-slate-500 mt-6">
            &copy; {{ date('Y') }} Welfare Benefits Board, Sri Lanka.
        </div>
    </div>

</body>
</html>
