<div class="max-w-md mx-auto mt-12 bg-mentega p-6 rounded-lg border border-espresso/20">
    <h1 class="text-2xl font-serif text-espresso mb-6">Masuk</h1>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Email</label>
            <input type="email" wire:model="email" class="w-full bg-krim border border-espresso/30 rounded px-3 py-2">
            @error('email') <p class="text-merah-bata text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Password</label>
            <input type="password" wire:model="password" class="w-full bg-krim border border-espresso/30 rounded px-3 py-2">
            @error('password') <p class="text-merah-bata text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" wire:model="remember" class="rounded border-espresso/40">
            Ingat saya
        </label>

        <button type="submit" class="w-full bg-karamel hover:bg-karamel-hover text-espresso font-medium rounded py-3 min-h-[44px] transition-colors duration-150">
            Masuk
        </button>
    </form>
</div>
