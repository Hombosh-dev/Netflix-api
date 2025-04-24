<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VideoPlayerName: string implements HasColor, HasIcon, HasLabel
{
    case KODIK = 'kodik';
    case ALOHA = 'aloha';

    
    public function getLabel(): ?string
    {
        return __('video_player_name.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::KODIK => 'info',   
            self::ALOHA => 'primary', 
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::KODIK => 'heroicon-o-play-circle',
            self::ALOHA => 'heroicon-o-video-camera',
        };
    }
}
