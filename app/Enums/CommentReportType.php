<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CommentReportType: string implements HasColor, HasIcon, HasLabel
{
    case INSULT = 'insult';
    case FLOOD_OFFTOP_MEANINGLESS = 'flood_offtop_meaningless';
    case AD_SPAM = 'ad_spam';
    case SPOILER = 'spoiler';
    case PROVOCATION_CONFLICT = 'provocation_conflict';
    case INAPPROPRIATE_LANGUAGE = 'inappropriate_language';
    case FORBIDDEN_UNNECESSARY_CONTENT = 'forbidden_unnecessary_content';
    case MEANINGLESS_EMPTY_TOPIC = 'meaningless_empty_topic';
    case DUPLICATE_TOPIC = 'duplicate_topic';

    
    public function getLabel(): ?string
    {
        return __('enums.comment_report.'.$this->value);
    }

    
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INSULT => 'danger',               
            self::FLOOD_OFFTOP_MEANINGLESS => 'gray', 
            self::AD_SPAM => 'warning',             
            self::SPOILER => 'info',                
            self::PROVOCATION_CONFLICT => 'danger',  
            self::INAPPROPRIATE_LANGUAGE => 'warning', 
            self::FORBIDDEN_UNNECESSARY_CONTENT => 'danger', 
            self::MEANINGLESS_EMPTY_TOPIC => 'gray',  
            self::DUPLICATE_TOPIC => 'primary',       
        };
    }

    
    public function getIcon(): ?string
    {
        return match ($this) {
            self::INSULT => 'heroicon-o-face-frown',
            self::FLOOD_OFFTOP_MEANINGLESS => 'heroicon-o-chat-bubble-oval-left-ellipsis',
            self::AD_SPAM => 'heroicon-o-megaphone',
            self::SPOILER => 'heroicon-o-eye-slash',
            self::PROVOCATION_CONFLICT => 'heroicon-o-fire',
            self::INAPPROPRIATE_LANGUAGE => 'heroicon-o-no-symbol',
            self::FORBIDDEN_UNNECESSARY_CONTENT => 'heroicon-o-shield-exclamation',
            self::MEANINGLESS_EMPTY_TOPIC => 'heroicon-o-document-text',
            self::DUPLICATE_TOPIC => 'heroicon-o-document-duplicate',
        };
    }
}
