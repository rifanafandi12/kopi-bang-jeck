<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Tunai = 'tunai';
    case Online = 'online';
    case BelumDipilih = 'belum_dipilih';
}
