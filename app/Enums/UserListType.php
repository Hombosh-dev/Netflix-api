<?php

namespace App\Enums;

enum UserListType: string
{
    case FAVORITE = 'Улюблене';
    case NOT_WATCHING = 'Не дивлюся';
    case WATCHING = 'Дивлюся';
    case PLANNED = 'В планах';
    case STOPPED = 'Закинув';
    case REWATCHING = 'Передивляюсь';
    case WATCHED = 'Переглянуто';
}
