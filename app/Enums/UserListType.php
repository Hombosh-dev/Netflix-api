<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserListType: string implements HasColor, HasIcon, HasLabel
{
    case FAVORITE = 'favorite';
    case NOT_WATCHING = 'not_watching';
    case WATCHING = 'watching';
    case PLANNED = 'planned';
    case STOPPED = 'stopped';
    case REWATCHING = 'rewatching';
    case WATCHED = 'watched';

    
    public function getLabel(): ?string
    {
        return __('enums.user_list_type.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FAVORITE => 'danger',     
            self::NOT_WATCHING => 'gray',   
            self::WATCHING => 'success',    
            self::PLANNED => 'info',       
            self::STOPPED => 'warning',    
            self::REWATCHING => 'primary', 
            self::WATCHED => 'success',    
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::FAVORITE => 'heroicon-o-heart',
            self::NOT_WATCHING => 'heroicon-o-x-mark',
            self::WATCHING => 'heroicon-o-play',
            self::PLANNED => 'heroicon-o-clock',
            self::STOPPED => 'heroicon-o-stop',
            self::REWATCHING => 'heroicon-o-arrow-path',
            self::WATCHED => 'heroicon-o-check',
        };
    }

    
    public function getMetaTitle(): string
    {
        return __('enums.user_list_type.meta_title.'.$this->value);
    }

    
    public function getMetaDescription(): string
    {
        return __('enums.user_list_type.meta_description.'.$this->value);
    }

    
    public function getMetaImage(): string
    {
        return __('enums.user_list_type.meta_image.'.$this->value);
    }
}
