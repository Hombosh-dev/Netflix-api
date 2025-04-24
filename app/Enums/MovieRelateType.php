<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MovieRelateType: string implements HasColor, HasIcon, HasLabel
{
    case SEASON = 'season';
    case SOURCE = 'source';
    case SEQUEL = 'sequel';
    case SIDE_STORY = 'side_story';
    case SUMMARY = 'summary';
    case OTHER = 'other';
    case ADAPTATION = 'adaptation';
    case ALTERNATIVE = 'alternative';
    case PREQUEL = 'prequel';

    
    public function getLabel(): ?string
    {
        return __('movie_relate_type.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SEASON => 'info',       
            self::SOURCE => 'gray',       
            self::SEQUEL => 'success',    
            self::SIDE_STORY => 'primary', 
            self::SUMMARY => 'warning',   
            self::OTHER => 'gray',        
            self::ADAPTATION => 'orange', 
            self::ALTERNATIVE => 'pink',  
            self::PREQUEL => 'danger',    
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::SEASON => 'heroicon-o-calendar',
            self::SOURCE => 'heroicon-o-book-open',
            self::SEQUEL => 'heroicon-o-arrow-right',
            self::SIDE_STORY => 'heroicon-o-arrow-path',
            self::SUMMARY => 'heroicon-o-document-text',
            self::OTHER => 'heroicon-o-ellipsis-horizontal',
            self::ADAPTATION => 'heroicon-o-film',
            self::ALTERNATIVE => 'heroicon-o-arrows-right-left',
            self::PREQUEL => 'heroicon-o-arrow-left',
        };
    }
}
