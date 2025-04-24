<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasColor, HasIcon, HasLabel
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    
    public function getLabel(): ?string
    {
        return __('enums.gender.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MALE => 'info',      
            self::FEMALE => 'pink',    
            self::OTHER => 'gray',     
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::MALE => 'heroicon-o-user',
            self::FEMALE => 'heroicon-o-user',
            self::OTHER => 'heroicon-o-user',
        };
    }


}
