<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AttachmentType: string implements HasColor, HasIcon, HasLabel
{
    case TRAILER = 'trailer';
    case TEASER = 'teaser';
    case BEHIND_THE_SCENES = 'behind_the_scenes';
    case INTERVIEW = 'interview';
    case CLIP = 'clip';
    case DELETED_SCENE = 'deleted_scene';
    case BLOOPER = 'blooper';
    case FEATURETTE = 'featurette';
    case PICTURE = 'picture';


    public function getLabel(): ?string
    {
        return __('enums.attachment_type.'.$this->value);
    }


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TRAILER => 'danger',
            self::TEASER => 'warning',
            self::BEHIND_THE_SCENES => 'info',
            self::INTERVIEW => 'success',
            self::CLIP => 'primary',
            self::DELETED_SCENE => 'gray',
            self::BLOOPER => 'pink',
            self::FEATURETTE => 'orange',
            self::PICTURE => 'indigo',
        };
    }


    public function getIcon(): ?string
    {
        return match ($this) {
            self::TRAILER => 'heroicon-o-film',
            self::TEASER => 'heroicon-o-eye',
            self::BEHIND_THE_SCENES => 'heroicon-o-camera',
            self::INTERVIEW => 'heroicon-o-microphone',
            self::CLIP => 'heroicon-o-play',
            self::DELETED_SCENE => 'heroicon-o-trash',
            self::BLOOPER => 'heroicon-o-face-smile',
            self::FEATURETTE => 'heroicon-o-star',
            self::PICTURE => 'heroicon-o-photo',
        };
    }
}
