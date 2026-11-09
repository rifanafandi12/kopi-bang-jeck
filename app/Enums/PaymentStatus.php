<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case BelumBayar = 'belum_bayar';
    case Menunggu = 'menunggu';
    case Lunas = 'lunas';
    case Gagal = 'gagal';
    case Kedaluwarsa = 'kedaluwarsa';
}
