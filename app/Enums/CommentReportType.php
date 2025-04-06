<?php

namespace App\Enums;

enum CommentReportType: string
{
    case INSULT = 'осквернення користувачів';
    case AD_SPAM = 'реклама / спам';
    case SPOILER = 'спойлер';
    case PROVOCATION_CONFLICT = 'провокації / конфлікти';
    case INAPPROPRIATE_LANGUAGE = 'ненормативна лексика';
}
