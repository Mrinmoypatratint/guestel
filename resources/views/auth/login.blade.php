<!doctype html>
<html lang="en" class="h-full bg-[#e8edf5] text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In · Guestel Operational Workspace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; background-color: #e8edf5; color: #1e293b; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-[#e8edf5] text-slate-800 antialiased p-4 sm:p-8 flex items-center justify-center" x-data="{
    email: 'admin@example.com',
    password: 'Admin12345!',
    setRole(r) {
        if (r === 'super_admin' || r === 'hotel_admin') {
            this.email = 'admin@example.com';
        } else if (r === 'housekeeping') {
            this.email = 'maria.santos@grandazure.com';
        } else if (r === 'chef') {
            this.email = 'chef.marcus@grandazure.com';
        }
        this.password = 'Admin12345!';
    }
}">

<div class="max-w-4xl w-full mx-auto space-y-6">

    <!-- Top Navigation / Return -->
    <div class="flex items-center justify-between px-2">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 neu-button px-4 py-2 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Overview</span>
        </a>

        <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Production Auth Gateway</span>
        </div>
    </div>

    <!-- Main Neumorphic Container -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-10 space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2 max-w-lg mx-auto">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl neu-button text-slate-900 font-extrabold text-2xl mx-auto mb-1">
                G
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Sign In to Guestel</h1>
            <p class="text-xs sm:text-sm text-slate-600">
                Choose a 1-click test login persona or enter your credentials below.
            </p>
        </div>

        <!-- ==================================================================== -->
        <!-- ⚡ 1-CLICK TEST LOGIN OPTIONS (NEUMORPHIC) -->
        <!-- ==================================================================== -->
        <div class="neu-inset rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="text-blue-600">⚡</span>
                    <span>1-Click Test Login (Instant Access)</span>
                </span>
                <span class="text-slate-500 font-mono text-[11px]">Pass: Admin12345!</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- 1. Super Admin -->
                <form method="POST" action="{{ route('login.test') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="role" value="super_admin">
                    <button type="submit" class="w-full p-3 rounded-xl neu-button text-left hover:scale-[1.02] transition cursor-pointer flex flex-col justify-between h-full space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🏢</span>
                            <span class="text-xs font-bold text-slate-900 leading-tight">Super Admin</span>
                        </div>
                        <span class="text-[10px] text-slate-500 block">SaaS Governance & Bills</span>
                        <span class="text-[11px] font-bold text-amber-700 block pt-1">⚡ Instant Login →</span>
                    </button>
                </form>

                <!-- 2. Hotel Admin -->
                <form method="POST" action="{{ route('login.test') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="role" value="hotel_admin">
                    <button type="submit" class="w-full p-3 rounded-xl neu-button text-left hover:scale-[1.02] transition cursor-pointer flex flex-col justify-between h-full space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🏨</span>
                            <span class="text-xs font-bold text-slate-900 leading-tight">Hotel Operations</span>
                        </div>
                        <span class="text-[10px] text-slate-500 block">Rooms, Chat & QR Center</span>
                        <span class="text-[11px] font-bold text-blue-700 block pt-1">⚡ Instant Login →</span>
                    </button>
                </form>

                <!-- 3. Housekeeping -->
                <form method="POST" action="{{ route('login.test') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="role" value="housekeeping">
                    <button type="submit" class="w-full p-3 rounded-xl neu-button text-left hover:scale-[1.02] transition cursor-pointer flex flex-col justify-between h-full space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🧹</span>
                            <span class="text-xs font-bold text-slate-900 leading-tight">Housekeeping</span>
                        </div>
                        <span class="text-[10px] text-slate-500 block">SLA Priority Cleaning</span>
                        <span class="text-[11px] font-bold text-purple-700 block pt-1">⚡ Instant Login →</span>
                    </button>
                </form>

                <!-- 4. Executive Chef -->
                <form method="POST" action="{{ route('login.test') }}" class="w-full">
                    @csrf
                    <input type="hidden" name="role" value="chef">
                    <button type="submit" class="w-full p-3 rounded-xl neu-button text-left hover:scale-[1.02] transition cursor-pointer flex flex-col justify-between h-full space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-base">👨‍🍳</span>
                            <span class="text-xs font-bold text-slate-900 leading-tight">Executive Chef</span>
                        </div>
                        <span class="text-[10px] text-slate-500 block">Kitchen Kanban & KDS</span>
                        <span class="text-[11px] font-bold text-rose-700 block pt-1">⚡ Instant Login →</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="relative flex py-1 items-center">
            <div class="flex-grow border-t border-slate-300"></div>
            <span class="flex-shrink mx-4 text-xs font-bold uppercase tracking-wider text-slate-400">or sign in manually</span>
            <div class="flex-grow border-t border-slate-300"></div>
        </div>

        <!-- Manual Login Form -->
        <form method="post" action="{{ route('login.store') }}" class="max-w-md mx-auto space-y-5">
            @csrf

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Work Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    x-model="email"
                    required 
                    autocomplete="email"
                    class="w-full px-4 py-3 rounded-xl neu-input text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
                    placeholder="name@example.com"
                />
                @error('email')
                    <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-blue-600 font-bold hover:underline">Forgot password?</a>
                </div>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    x-model="password"
                    required 
                    autocomplete="current-password"
                    class="w-full px-4 py-3 rounded-xl neu-input text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
                    placeholder="••••••••••••"
                />
                @error('password')
                    <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600">
                    <input type="checkbox" name="remember" class="rounded neu-inset text-blue-600 focus:ring-0">
                    <span>Keep me signed in</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3.5 px-6 rounded-xl font-bold text-sm text-white neu-btn-primary hover:brightness-110 transition cursor-pointer">
                Sign In to Workspace
            </button>
        </form>

    </div>

    <!-- Footer info -->
    <div class="text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Guestel · Talisha Software. All rights reserved.
    </div>

</div>

</body>
</html>
