<?php

namespace App\Enums;

enum Status: string
{
    case ANONS = 'anons';
    case ONGOING = 'ongoing';
    case RELEASED = 'released';
    case CANCELED = 'canceled';
    case RUMORED = 'rumored';
}
