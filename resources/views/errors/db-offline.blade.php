<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Offline - IT Strategic Planning System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }
        .bg-gradient {
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="bg-gradient min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-8 animate-fade-in">
        <div class="flex justify-center">
            <div class="relative">
                <div class="absolute inset-0 bg-blue-500 blur-2xl opacity-20 animate-pulse"></div>
                <img src="/logo.png" alt="Logo" class="relative h-20 w-auto">
            </div>
        </div>

        <div class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold tracking-wider uppercase">
                <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                Koneksi Terputus
            </div>
            
            <h1 class="text-3xl font-bold tracking-tight text-white">Database Offline</h1>
            
            <p class="text-slate-400 leading-relaxed">
                Maaf, sistem tidak dapat terhubung ke database cloud saat ini. Hal ini mungkin dikarenakan koneksi internet Anda atau server database sedang dalam pemeliharaan.
            </p>
        </div>

        <div class="bg-slate-800/50 border border-white/5 rounded-2xl p-6 space-y-4 shadow-xl backdrop-blur-sm">
            <div class="flex items-start gap-4 text-left">
                <div class="h-10 w-10 shrink-0 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Apa yang bisa saya lakukan?</h4>
                    <p class="text-xs text-slate-500 mt-1">Pastikan koneksi internet stabil dan coba muat ulang halaman dalam beberapa saat.</p>
                </div>
            </div>

            <button onclick="window.location.reload()" class="w-full py-3 px-4 bg-white text-slate-900 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all active:scale-[0.98] shadow-lg shadow-white/10">
                Muat Ulang Halaman
            </button>
        </div>

        <div class="flex flex-col gap-2">
            <p class="text-[10px] text-slate-600 uppercase tracking-widest font-bold">IT Strategic Planning System</p>
            <p class="text-[9px] text-slate-700">Aiven Cloud Database Connection Service</p>
        </div>
    </div>
</body>
</html>
