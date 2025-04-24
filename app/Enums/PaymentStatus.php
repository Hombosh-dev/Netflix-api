<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    
    public function getLabel(): ?string
    {
        return __('enums.payment_status.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',   
            self::SUCCESS => 'success',   
            self::FAILED => 'danger',     
            self::REFUNDED => 'info',     
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
            self::SUCCESS => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-circle',
            self::REFUNDED => 'heroicon-o-arrow-path',
        };
    }

    
    public function getMetaTitle(): string
    {
        return __('enums.payment_status.meta_title.'.$this->value);
    }

    
    public function getMetaDescription(): string
    {
        return __('enums.payment_status.meta_description.'.$this->value);
    }

    
    public function getMetaImage(): string
    {
        return __('enums.payment_status.meta_image.'.$this->value);
    }
}
