<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NexaCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%); }
    </style>
</head>
<body class="bg-[#0b0b14] text-white min-h-screen flex items-center justify-center p-6 bg-mesh">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/20">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">NexaCRM</h1>
            <p class="text-gray-400 mt-2">Manage your workspace with elegance.</p>
        </div>

        <!-- Card -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Email Address</label>
                    <input type="email" name="email" required autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                        placeholder="name@company.com">
                    @error('email')<p class="text-red-400 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300">Forgot?</a>
                    </div>
                    <input type="password" name="password" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                        placeholder="••••••••">
                    @error('password')<p class="text-red-400 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-white/10 bg-white/5 text-indigo-500 focus:ring-indigo-500/50">
                    <label for="remember" class="ml-2 text-sm text-gray-400">Keep me logged in</label>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-sm text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-indigo-400 font-bold hover:underline">Create one</a>
        </p>
    </div>

</body>
</html>
