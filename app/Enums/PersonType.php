<?php

namespace App\Enums;

enum PersonType: string
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
}
