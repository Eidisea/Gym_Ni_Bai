<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 2.75rem; }
        .eye-toggle {
            position: absolute; top: 50%; right: 0.75rem;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #6b7280; padding: 0; display: flex; align-items: center;
        }
        .eye-toggle:hover { color: #111827; }
        .eye-toggle svg { width: 1.25rem; height: 1.25rem; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-white">Management Portal</h1>
            <p class="text-sm text-gray-400 mt-1">Authorized personnel only.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-900/50 border border-red-700 p-4">
                <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('management.login.post') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       required autofocus autocomplete="email"
                       class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white
                              px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                              @error('email') border-red-500 @enderror">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <div class="password-wrapper">
                    <input id="password" name="password" type="password"
                           required autocomplete="current-password"
                           class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white
                                  px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="button" class="eye-toggle" data-target="password"
                            aria-label="Toggle password" style="color: #9ca3af;">
                        <svg class="icon-eye-open" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg class="icon-eye-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           py-2.5 rounded-lg transition duration-150 text-sm">
                Sign In to Management
            </button>

            <p class="text-center text-sm text-gray-500 mt-4">
                Need an account?
                <a href="{{ route('management.register') }}" class="text-indigo-400 hover:underline">Request access</a>
            </p>
        </form>
    </div>

    <script>
        document.querySelectorAll('.eye-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input    = document.getElementById(this.getAttribute('data-target'));
                const eyeOpen  = this.querySelector('.icon-eye-open');
                const eyeSlash = this.querySelector('.icon-eye-slash');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeSlash.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeSlash.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>