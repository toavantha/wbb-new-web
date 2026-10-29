<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request CMS Access | Welfare Benefits Board</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/welfare-benefits-board-vacancies.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-900 p-4 font-sans text-slate-800 antialiased">
    <main class="w-full max-w-md">
        <div class="mb-8 text-center">
            <img src="{{ asset('images/wbb-logo.png') }}" alt="Welfare Benefits Board" class="mx-auto mb-3 h-16 w-auto">
            <h1 class="text-xl font-extrabold text-white">WELFARE BENEFITS BOARD</h1>
            <p class="mt-1 text-xs font-medium uppercase tracking-wider text-amber-400">Website Content Management System</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-2xl">
            <h2 class="mb-1 text-lg font-bold text-slate-900">Request CMS Access</h2>
            <p class="mb-6 text-xs text-slate-500">Verify your email address first. An administrator must then approve your account before you can sign in.</p>

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 px-3 py-2 text-xs text-rose-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="191" autocomplete="name" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="191" autocomplete="email" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                    <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                    <p class="mt-1 text-xs text-slate-500">At least 8 characters.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm focus:border-wbb-600 focus:ring-2 focus:ring-wbb-600">
                </div>

                <button type="submit" class="mt-2 w-full rounded-xl bg-wbb-800 px-4 py-3 text-sm font-bold text-white shadow-md transition hover:bg-wbb-700">
                    Submit Registration Request
                </button>
            </form>

            <div class="mt-6 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">
                Already registered?
                <a href="{{ route('admin.login') }}" class="font-semibold text-wbb-700 hover:underline">Return to sign in</a>
            </div>
        </div>
    </main>
</body>
</html>
