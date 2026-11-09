<div class="max-w-md mx-auto">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-widest text-teks-aksen">Meja {{ $table->nomor }}</p>
        <h1 class="text-3xl font-serif text-espresso mt-1">Selamat datang</h1>
        <p class="text-teks-sekunder mt-2 text-sm">
            Isi nama Anda dulu, lalu pilih menu. Pesanan akan langsung masuk ke kasir.
        </p>
    </div>

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label for="nama" class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Nama *</label>
            <input
                id="nama"
                type="text"
                wire:model="nama"
                class="w-full bg-mentega border border-espresso/30 rounded px-3 py-2 text-espresso focus:outline-none focus:border-espresso"
                required
            >
            @error('nama') <p class="text-merah-bata text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="telepon" class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Nomor HP (opsional)</label>
            <input
                id="telepon"
                type="text"
                wire:model="telepon"
                class="w-full bg-mentega border border-espresso/30 rounded px-3 py-2 text-espresso focus:outline-none focus:border-espresso"
            >
            @error('telepon') <p class="text-merah-bata text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Email (opsional)</label>
            <input
                id="email"
                type="email"
                wire:model="email"
                class="w-full bg-mentega border border-espresso/30 rounded px-3 py-2 text-espresso focus:outline-none focus:border-espresso"
            >
            @error('email') <p class="text-merah-bata text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="catatan" class="block text-xs uppercase tracking-widest text-teks-aksen mb-1">Catatan (opsional)</label>
            <textarea
                id="catatan"
                wire:model="catatan"
                rows="2"
                class="w-full bg-mentega border border-espresso/30 rounded px-3 py-2 text-espresso focus:outline-none focus:border-espresso"
            ></textarea>
        </div>

        <button
            type="submit"
            class="w-full bg-karamel hover:bg-karamel-hover text-espresso font-medium rounded px-4 py-3 min-h-[44px] transition-colors duration-150"
            wire:loading.attr="disabled"
        >
            Lihat menu
        </button>
    </form>
</div>
