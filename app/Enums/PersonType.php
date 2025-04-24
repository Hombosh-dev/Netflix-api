<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PersonType: string implements HasColor, HasIcon, HasLabel
{
    case ACTOR = 'actor';
    case CHARACTER = 'character';
    case DIRECTOR = 'director';
    case PRODUCER = 'producer';
    case WRITER = 'writer';
    case EDITOR = 'editor';
    case CINEMATOGRAPHER = 'cinematographer';
    case COMPOSER = 'composer';
    case ART_DIRECTOR = 'art_director';
    case SOUND_DESIGNER = 'sound_designer';
    case COSTUME_DESIGNER = 'costume_designer';
    case MAKEUP_ARTIST = 'makeup_artist';
    case VOICE_ACTOR = 'voice_actor';
    case STUNT_PERFORMER = 'stunt_performer';
    case ASSISTANT_DIRECTOR = 'assistant_director';
    case PRODUCER_ASSISTANT = 'producer_assistant';
    case SCRIPT_SUPERVISOR = 'script_supervisor';
    case PRODUCTION_DESIGNER = 'production_designer';
    case VISUAL_EFFECTS_SUPERVISOR = 'visual_effects_supervisor';


    public function getLabel(): ?string
    {
        return __('enums.person_type.'.$this->value);
    }


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTOR => 'primary',
            self::CHARACTER => 'info',
            self::DIRECTOR => 'danger',
            self::PRODUCER => 'success',
            self::WRITER => 'warning',
            self::EDITOR => 'gray',
            self::CINEMATOGRAPHER => 'info',
            self::COMPOSER => 'primary',
            self::ART_DIRECTOR => 'success',
            self::SOUND_DESIGNER => 'warning',
            self::COSTUME_DESIGNER => 'info',
            self::MAKEUP_ARTIST => 'primary',
            self::VOICE_ACTOR => 'success',
            self::STUNT_PERFORMER => 'danger',
            self::ASSISTANT_DIRECTOR => 'warning',
            self::PRODUCER_ASSISTANT => 'gray',
            self::SCRIPT_SUPERVISOR => 'info',
            self::PRODUCTION_DESIGNER => 'primary',
            self::VISUAL_EFFECTS_SUPERVISOR => 'success',
        };
    }


    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTOR => 'heroicon-o-user',
            self::CHARACTER => 'heroicon-o-user-circle',
            self::DIRECTOR => 'heroicon-o-video-camera',
            self::PRODUCER => 'heroicon-o-currency-dollar',
            self::WRITER => 'heroicon-o-document-text',
            self::EDITOR => 'heroicon-o-scissors',
            self::CINEMATOGRAPHER => 'heroicon-o-camera',
            self::COMPOSER => 'heroicon-o-musical-note',
            self::ART_DIRECTOR => 'heroicon-o-paint-brush',
            self::SOUND_DESIGNER => 'heroicon-o-speaker-wave',
            self::COSTUME_DESIGNER => 'heroicon-o-speaker-wave',
            self::MAKEUP_ARTIST => 'heroicon-o-face-smile',
            self::VOICE_ACTOR => 'heroicon-o-microphone',
            self::STUNT_PERFORMER => 'heroicon-o-bolt',
            self::ASSISTANT_DIRECTOR => 'heroicon-o-user-plus',
            self::PRODUCER_ASSISTANT => 'heroicon-o-user-plus',
            self::SCRIPT_SUPERVISOR => 'heroicon-o-clipboard-document-check',
            self::PRODUCTION_DESIGNER => 'heroicon-o-paint-brush',
            self::VISUAL_EFFECTS_SUPERVISOR => 'heroicon-o-sparkles',
        };
    }


    public function getMetaTitle(): string
    {
        return __('enums.person_type.meta_title.'.$this->value);
    }


    public function getMetaDescription(): string
    {
        return __('enums.person_type.meta_description.'.$this->value);
    }


    public function getMetaImage(): string
    {
        return __('enums.person_type.meta_image.'.$this->value);
    }
}
