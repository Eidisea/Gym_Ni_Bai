<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Staff Account — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 2.75rem; }
        .eye-toggle {
            position: absolute; top: 50%; right: 0.75rem;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #9ca3af; padding: 0; display: flex; align-items: center;
        }
        .eye-toggle svg { width: 1.25rem; height: 1.25rem; pointer-events: none; }
        .conditional-field { display: none; }
        .conditional-field.visible { display: block; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-lg bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-white">Create Internal Account</h1>
            <p class="text-sm text-gray-400 mt-1">Admin, Staff, and Trainer accounts only.</p>
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

        <form method="POST" action="{{ route('management.register.post') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">First Name <span class="text-red-400">*</span></label>
                    <input name="first_name" type="text" value="{{ old('first_name') }}" required
                           class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Last Name <span class="text-red-400">*</span></label>
                    <input name="last_name" type="text" value="{{ old('last_name') }}" required
                           class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Email <span class="text-red-400">*</span></label>
                <input name="email" type="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Role Selector --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Role <span class="text-red-400">*</span></label>
                <select name="role_id" id="role_select" required
                        class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Select a role —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->role_id }}"
                                data-role="{{ $role->role_name }}"
                                {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Staff/Admin conditional field --}}
            <div id="field_department" class="conditional-field mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Department</label>
                <input name="department" type="text" value="{{ old('department') }}"
                       placeholder="e.g., Front Desk, Admin"
                       class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Password <span class="text-red-400">*</span></label>
                <div class="password-wrapper">
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Min. 8 characters">
                    <button type="button" class="eye-toggle" data-target="password" aria-label="Toggle">
                        <svg class="icon-eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg class="icon-eye-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-1">Confirm Password <span class="text-red-400">*</span></label>
                <div class="password-wrapper">
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full rounded-lg bg-gray-700 border border-gray-600 text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="button" class="eye-toggle" data-target="password_confirmation" aria-label="Toggle">
                        <svg class="icon-eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg class="icon-eye-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                Create Account
            </button>
        </form>
    </div>

    <script>
        // Eye toggle
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

        // Conditional profile fields based on role
        const roleSelect   = document.getElementById('role_select');
        const deptField    = document.getElementById('field_department');

        function updateConditionalFields() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName       = selectedOption ? selectedOption.getAttribute('data-role') : '';

            deptField.classList.toggle('visible', roleName === 'Admin' || roleName === 'Staff');
        }

        roleSelect.addEventListener('change', updateConditionalFields);
        updateConditionalFields(); // Run on page load to handle old() repopulation
    </script>
</body>
</html>