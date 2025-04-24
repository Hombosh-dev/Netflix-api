<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Kind: string implements HasColor, HasIcon, HasLabel
{
    case MOVIE = 'movie';
    case TV_SERIES = 'tv_series';
    case ANIMATED_MOVIE = 'animated_movie';
    case ANIMATED_SERIES = 'animated_series';

    
    public function getLabel(): ?string
    {
        return __('enums.kind.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MOVIE => 'danger',           
            self::TV_SERIES => 'primary',      
            self::ANIMATED_MOVIE => 'success', 
            self::ANIMATED_SERIES => 'info',   
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::MOVIE => 'heroicon-o-film',
            self::TV_SERIES => 'heroicon-o-tv',
            self::ANIMATED_MOVIE => 'heroicon-o-sparkles',
            self::ANIMATED_SERIES => 'heroicon-o-play',
        };
    }

    
    public function name(): string
    {
        return __('enums.kind.'.$this->value);
    }

    
    public function description(): string
    {
        return __('enums.kind.description.'.$this->value);
    }

    
    public function getMetaTitle(): string
    {
        return __('enums.kind.meta_title.'.$this->value);
    }

    
    public function getMetaDescription(): string
    {
        return __('enums.kind.meta_description.'.$this->value);
    }

    
    public function getMetaImage(): string
    {
        return __('enums.kind.meta_image.'.$this->value);
    }

    
    public function metaImage(): string
    {
        return $this->getMetaImage();
    }
}
