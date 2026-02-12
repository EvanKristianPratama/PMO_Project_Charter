<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'PMO Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    <script>
        (function () {
            try {
                const saved = localStorage.getItem('darkMode');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = saved !== null ? saved === 'true' : prefersDark;

                document.documentElement.classList.toggle('dark', isDark);
            } catch (_) {}
        })();
    </script>
</head>
<body class="antialiased bg-slate-50 text-slate-900 dark:bg-[#0f0f0f] dark:text-slate-100 transition-colors duration-300">
    @inertia
</body>
</html>
