<?php

namespace App\Enums;

enum EventActor: string
{
    case Pelanggan = 'pelanggan';
    case Kasir = 'kasir';
    case Sistem = 'sistem';
}
