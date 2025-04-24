<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasColor, HasIcon, HasLabel
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';

    
    public function getLabel(): ?string
    {
        return __('enums.role.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::USER => 'gray',       
            self::ADMIN => 'danger',    
            self::MODERATOR => 'warning', 
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::USER => 'heroicon-o-user',
            self::ADMIN => 'heroicon-o-key',
            self::MODERATOR => 'heroicon-o-shield-check',
        };
    }


}
