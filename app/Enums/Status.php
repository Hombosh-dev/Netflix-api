<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case ANONS = 'anons';
    case ONGOING = 'ongoing';
    case RELEASED = 'released';
    case CANCELED = 'canceled';
    case RUMORED = 'rumored';

    
    public function getLabel(): ?string
    {
        return __('enums.status.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ANONS => 'info',      
            self::ONGOING => 'success',  
            self::RELEASED => 'primary', 
            self::CANCELED => 'danger',  
            self::RUMORED => 'gray',    
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::ANONS => 'heroicon-o-megaphone',
            self::ONGOING => 'heroicon-o-play',
            self::RELEASED => 'heroicon-o-check-badge',
            self::CANCELED => 'heroicon-o-x-circle',
            self::RUMORED => 'heroicon-o-chat-bubble-left-ellipsis',
        };
    }

    
    public function getMetaTitle(): string
    {
        return __('enums.status.meta_title.'.$this->value);
    }

    
    public function getMetaDescription(): string
    {
        return __('enums.status.meta_description.'.$this->value);
    }

    
    public function getMetaImage(): string
    {
        return __('enums.status.meta_image.'.$this->value);
    }
}
