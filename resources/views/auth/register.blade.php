<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NexaCRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh { background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%); }
    </style>
</head>
<body class="bg-[#0b0b14] text-white min-h-screen flex items-center justify-center p-6 bg-mesh">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Join NexaCRM</h1>
            <p class="text-gray-400 mt-2">Start collaborating with your team today.</p>
        </div>

        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Full Name</label>
                    <input type="text" name="name" required autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                        placeholder="John Doe">
                    @error('name')<p class="text-red-400 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                        placeholder="name@company.com">
                    @error('email')<p class="text-red-400 text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Confirm</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror

                <button type="submit"
                    class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-500/25 transition-all transform hover:-translate-y-0.5 mt-4">
                    Get Started Free
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-sm text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="text-indigo-400 font-bold hover:underline">Sign In</a>
        </p>
    </div>

</body>
</html>
