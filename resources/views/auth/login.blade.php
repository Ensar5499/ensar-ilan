<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f0f2f5] antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        
        <div class="w-full max-w-[450px] bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] p-10 md:p-14">
            
            <div class="flex justify-center mb-10">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Hoş Geldiniz</h1>
                <p class="text-gray-400 mt-2 font-medium">Lütfen bilgilerinizi girerek oturum açın</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-100">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-600 text-xs font-bold uppercase tracking-wide text-center">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">E-posta</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 outline-none text-gray-700 font-medium"
                           placeholder="mail@domain.com">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">Şifre</label>
                    </div>
                    <input type="password" name="password" required
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 outline-none text-gray-700 font-medium"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-sm px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm">
                        <span class="ml-3 text-gray-500 group-hover:text-gray-800 transition-colors">Beni Hatırla</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition-all">Unuttum?</a>
                    @endif
                </div>

                <button type="submit" 
                        class="w-full py-4 bg-gray-900 hover:bg-black text-white font-bold rounded-2xl shadow-xl shadow-gray-200 transition-all duration-300 active:scale-[0.97]">
                    Giriş Yap
                </button>

                @if (Route::has('register'))
                    <p class="text-center text-sm text-gray-400 font-medium pt-4">
                        Hesabınız yok mu? 
                        <a href="{{ route('register') }}" class="text-indigo-600 font-extrabold hover:underline ml-1">Kayıt Ol</a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</body>
</html>