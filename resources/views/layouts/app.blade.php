<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kopi Bang Jeck' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-krim text-espresso antialiased">
    <header class="border-b-2 border-espresso bg-espresso text-krim">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <span class="text-2xl font-serif tracking-tight text-mentega">Kopi Bang Jeck</span>
            </a>
            <nav class="text-sm uppercase tracking-widest text-krim/80">
                <a href="/" class="hover:text-mentega">Beranda</a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="border-t border-espresso/20 mt-12">
        <div class="max-w-5xl mx-auto px-4 py-4 text-xs text-teks-sekunder">
            &copy; {{ date('Y') }} Kopi Bang Jeck &middot; Pesan dari meja, tanpa menunggu.
        </div>
    </footer>

    @livewireScripts
</body>
</html>
