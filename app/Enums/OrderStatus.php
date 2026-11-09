<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Dikonfirmasi = 'dikonfirmasi';
    case Diterima = 'diterima';
    case Diproses = 'diproses';
    case Siap = 'siap';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
}
