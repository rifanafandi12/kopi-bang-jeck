<div class="flex flex-col items-center justify-center p-6 space-y-4">
    <h2 class="text-2xl font-bold">Meja {{ $nomor }}</h2>

    <div class="p-4 bg-white rounded-lg border-2 border-gray-200">
        <img src="{{ $dataUri }}" alt="QR Code Meja {{ $nomor }}" class="w-64 h-64">
    </div>

    <p class="text-sm text-center text-gray-500 max-w-xs">
        Cetak gambar di atas dan tempelkan di meja nomor {{ $nomor }}. Pelanggan dapat memindai QR ini untuk memesan.
    </p>

    {{-- Tombol Print bawaan browser --}}
    <button onclick="window.print()" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded shadow hover:bg-primary-500">
        Cetak QR Code
    </button>
</div>

{{-- CSS Khusus agar saat di-print hanya QR-nya saja yang muncul --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .fi-modal-window, .fi-modal-window * {
        visibility: visible;
    }
    .fi-modal-window {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
    }
    .fi-modal-header, .fi-modal-footer {
        display: none !important;
    }
}
</style>
