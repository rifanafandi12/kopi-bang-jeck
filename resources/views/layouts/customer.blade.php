<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Kopi Bang Jeck' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-krim text-espresso antialiased font-sans">

    {{-- Header Minimalis --}}
    <header class="border-b border-espresso/10 bg-krim sticky top-0 z-40">
        <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-center">
            <span class="font-serif text-xl tracking-tight text-espresso">Kopi Bang Jeck</span>
        </div>
    </header>

    {{-- Konten Utama --}}
    {{-- pb-24 memberi ruang agar konten tidak tertutup bottom nav --}}
    <main class="max-w-md mx-auto px-4 pt-4 pb-24 min-h-[calc(100vh-120px)]">
        {{ $slot }}
    </main>

    {{-- Bottom Navigation (Hanya untuk Pelanggan) --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-mentega border-t border-espresso/20 z-50 pb-safe">
        <div class="max-w-md mx-auto flex justify-around items-center h-16">

            {{-- Tombol Menu --}}
            <a href="{{ route('customer.menu', ['token' => request()->route('token')]) }}"
               class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('customer.menu') ? 'text-karamel' : 'text-teks-sekunder hover:text-espresso' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-[10px] font-medium uppercase tracking-wider">Menu</span>
            </a>

            {{-- Tombol Status --}}
            <a href="{{ route('customer.status', ['token' => request()->route('token')]) }}"
               class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('customer.status') ? 'text-karamel' : 'text-teks-sekunder hover:text-espresso' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span class="text-[10px] font-medium uppercase tracking-wider">Pesanan</span>
            </a>

        </div>
    </nav>

    @livewireScripts
</body>
</html>
