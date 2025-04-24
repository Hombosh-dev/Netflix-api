<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ApiSourceName: string implements HasColor, HasIcon, HasLabel
{
    case TMDB = 'tmdb';
    case SHIKI = 'shiki';
    case IMDB = 'imdb';
    case ANILIST = 'anilist';

    
    public function getLabel(): ?string
    {
        return __('api_source_name.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TMDB => 'success',  
            self::SHIKI => 'primary',  
            self::IMDB => 'warning',  
            self::ANILIST => 'info',  
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::TMDB => 'heroicon-o-film',
            self::SHIKI => 'heroicon-o-sun',
            self::IMDB => 'heroicon-o-star',
            self::ANILIST => 'heroicon-o-book-open',
        };
    }
}
