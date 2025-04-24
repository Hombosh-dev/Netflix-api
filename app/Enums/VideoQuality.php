<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VideoQuality: string implements HasColor, HasIcon, HasLabel
{
    case SD = 'sd';
    case HD = 'hd';
    case FULL_HD = 'full_hd';
    case UHD = 'uhd';


    public function getLabel(): ?string
    {
        return __('enums.video_quality.'.$this->value);
    }


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SD => 'gray',
            self::HD => 'info',
            self::FULL_HD => 'success',
            self::UHD => 'primary',
        };
    }


    public function getIcon(): ?string
    {
        return match ($this) {
            self::SD => 'heroicon-o-eye',
            self::HD => 'heroicon-o-eye',
            self::FULL_HD => 'heroicon-o-eye',
            self::UHD => 'heroicon-o-eye',
        };
    }


    public function getMetaTitle(): string
    {
        return __("video_quality.meta_title.{$this->value}");
    }


    public function getMetaDescription(): string
    {
        return __("video_quality.meta_description.{$this->value}");
    }


    public function getMetaImage(): string
    {
        return __("video_quality.meta_image.{$this->value}");
    }
}
