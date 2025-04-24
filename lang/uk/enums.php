<?php

return [

    'gender' => [
        'male' => 'Чоловіча',
        'female' => 'Жіноча',
        'other' => 'Інша',

        'meta_title' => [
            'male' => 'Чоловіча стать | Netflix',
            'female' => 'Жіноча стать | Netflix',
            'other' => 'Інша стать | Netflix',
        ],
        'meta_description' => [
            'male' => 'Контент для чоловічої аудиторії',
            'female' => 'Контент для жіночої аудиторії',
            'other' => 'Контент для всіх',
        ],
        'meta_image' => [
            'male' => '/images/seo/gender-male.jpg',
            'female' => '/images/seo/gender-female.jpg',
            'other' => '/images/seo/gender-other.jpg',
        ],
    ],


    'role' => [
        'user' => 'Користувач',
        'admin' => 'Адміністратор',
        'moderator' => 'Модератор',

        'meta_title' => [
            'user' => 'Користувач | Netflix',
            'admin' => 'Адміністратор | Netflix',
            'moderator' => 'Модератор | Netflix',
        ],
        'meta_description' => [
            'user' => 'Звичайний користувач платформи',
            'admin' => 'Адміністратор з повними правами доступу',
            'moderator' => 'Модератор з обмеженими правами адміністрування',
        ],
        'meta_image' => [
            'user' => '/images/seo/role-user.jpg',
            'admin' => '/images/seo/role-admin.jpg',
            'moderator' => '/images/seo/role-moderator.jpg',
        ],
    ],


    'payment_status' => [
        'pending' => 'В очікуванні',
        'success' => 'Успішно',
        'failed' => 'Невдало',
        'refunded' => 'Повернуто',

        'meta_title' => [
            'pending' => 'Платіж в очікуванні | Netflix',
            'success' => 'Успішний платіж | Netflix',
            'failed' => 'Невдалий платіж | Netflix',
            'refunded' => 'Повернутий платіж | Netflix',
        ],
        'meta_description' => [
            'pending' => 'Ваш платіж в очікуванні підтвердження',
            'success' => 'Ваш платіж успішно оброблено',
            'failed' => 'Виникла помилка при обробці вашого платежу',
            'refunded' => 'Кошти за ваш платіж було повернуто',
        ],
        'meta_image' => [
            'pending' => '/images/seo/payment-pending.jpg',
            'success' => '/images/seo/payment-success.jpg',
            'failed' => '/images/seo/payment-failed.jpg',
            'refunded' => '/images/seo/payment-refunded.jpg',
        ],
    ],


    'user_list_type' => [
        'favorite' => 'Улюблене',
        'not_watching' => 'Не дивлюся',
        'watching' => 'Дивлюся',
        'planned' => 'В планах',
        'stopped' => 'Закинув',
        'rewatching' => 'Передивляюсь',
        'watched' => 'Переглянуто',

        'meta_title' => [
            'favorite' => 'Улюблене | Netflix',
            'not_watching' => 'Не дивлюся | Netflix',
            'watching' => 'Дивлюся | Netflix',
            'planned' => 'В планах | Netflix',
            'stopped' => 'Закинув | Netflix',
            'rewatching' => 'Передивляюсь | Netflix',
            'watched' => 'Переглянуто | Netflix',
        ],
        'meta_description' => [
            'favorite' => 'Ваші улюблені фільми та серіали',
            'not_watching' => 'Фільми та серіали, які ви не дивитесь',
            'watching' => 'Фільми та серіали, які ви зараз дивитесь',
            'planned' => 'Фільми та серіали, які ви плануєте подивитись',
            'stopped' => 'Фільми та серіали, які ви перестали дивитись',
            'rewatching' => 'Фільми та серіали, які ви передивляєтесь',
            'watched' => 'Фільми та серіали, які ви вже переглянули',
        ],
        'meta_image' => [
            'favorite' => '/images/seo/list-favorite.jpg',
            'not_watching' => '/images/seo/list-not-watching.jpg',
            'watching' => '/images/seo/list-watching.jpg',
            'planned' => '/images/seo/list-planned.jpg',
            'stopped' => '/images/seo/list-stopped.jpg',
            'rewatching' => '/images/seo/list-rewatching.jpg',
            'watched' => '/images/seo/list-watched.jpg',
        ],
    ],


    'status' => [
        'anons' => 'Анонс',
        'ongoing' => 'Виходить',
        'released' => 'Завершено',
        'canceled' => 'Скасовано',
        'rumored' => 'Чутки',

        'meta_title' => [
            'anons' => 'Анонсовані фільми та серіали | Netflix',
            'ongoing' => 'Фільми та серіали, що виходять | Netflix',
            'released' => 'Завершені фільми та серіали | Netflix',
            'canceled' => 'Скасовані фільми та серіали | Netflix',
            'rumored' => 'Фільми та серіали в чутках | Netflix',
        ],
        'meta_description' => [
            'anons' => 'Фільми та серіали, які скоро вийдуть',
            'ongoing' => 'Фільми та серіали, які зараз виходять',
            'released' => 'Фільми та серіали, які вже завершені',
            'canceled' => 'Фільми та серіали, які були скасовані',
            'rumored' => 'Фільми та серіали, про які ходять чутки',
        ],
        'meta_image' => [
            'anons' => '/images/seo/status-anons.jpg',
            'ongoing' => '/images/seo/status-ongoing.jpg',
            'released' => '/images/seo/status-released.jpg',
            'canceled' => '/images/seo/status-canceled.jpg',
            'rumored' => '/images/seo/status-rumored.jpg',
        ],
    ],


    'person_type' => [
        'actor' => 'Актор',
        'character' => 'Персонаж',
        'director' => 'Режисер',
        'producer' => 'Продюсер',
        'writer' => 'Сценарист',
        'editor' => 'Монтажер',
        'cinematographer' => 'Оператор',
        'composer' => 'Композитор',
        'art_director' => 'Художник-постановник',
        'sound_designer' => 'Звукорежисер',
        'costume_designer' => 'Художник по костюмах',
        'makeup_artist' => 'Гример',
        'voice_actor' => 'Актор озвучення',
        'stunt_performer' => 'Каскадер',
        'assistant_director' => 'Помічник режисера',
        'producer_assistant' => 'Помічник продюсера',
        'script_supervisor' => 'Супервайзер сценарію',
        'production_designer' => 'Художник-постановник',
        'visual_effects_supervisor' => 'Супервайзер візуальних ефектів',

        'meta_title' => [
            'actor' => 'Актори | Netflix',
            'character' => 'Персонажі | Netflix',
            'director' => 'Режисери | Netflix',
            'producer' => 'Продюсери | Netflix',
            'writer' => 'Сценаристи | Netflix',
            'editor' => 'Монтажери | Netflix',
            'cinematographer' => 'Оператори | Netflix',
            'composer' => 'Композитори | Netflix',
            'art_director' => 'Художники-постановники | Netflix',
            'sound_designer' => 'Звукорежисери | Netflix',
            'costume_designer' => 'Художники по костюмах | Netflix',
            'makeup_artist' => 'Гримери | Netflix',
            'voice_actor' => 'Актори озвучення | Netflix',
            'stunt_performer' => 'Каскадери | Netflix',
            'assistant_director' => 'Помічники режисера | Netflix',
            'producer_assistant' => 'Помічники продюсера | Netflix',
            'script_supervisor' => 'Супервайзери сценарію | Netflix',
            'production_designer' => 'Художники-постановники | Netflix',
            'visual_effects_supervisor' => 'Супервайзери візуальних ефектів | Netflix',
        ],
        'meta_description' => [
            'actor' => 'Актори, які знімаються у фільмах та серіалах',
            'character' => 'Персонажі фільмів та серіалів',
            'director' => 'Режисери, які створюють фільми та серіали',
            'producer' => 'Продюсери, які фінансують фільми та серіали',
            'writer' => 'Сценаристи, які пишуть сценарії для фільмів та серіалів',
            'editor' => 'Монтажери, які монтують фільми та серіали',
            'cinematographer' => 'Оператори, які знімають фільми та серіали',
            'composer' => 'Композитори, які пишуть музику для фільмів та серіалів',
            'art_director' => 'Художники-постановники, які створюють візуальний стиль фільмів та серіалів',
            'sound_designer' => 'Звукорежисери, які створюють звукове оформлення фільмів та серіалів',
            'costume_designer' => 'Художники по костюмах, які створюють костюми для фільмів та серіалів',
            'makeup_artist' => 'Гримери, які створюють грим для фільмів та серіалів',
            'voice_actor' => 'Актори озвучення, які озвучують персонажів фільмів та серіалів',
            'stunt_performer' => 'Каскадери, які виконують трюки у фільмах та серіалах',
            'assistant_director' => 'Помічники режисера, які допомагають режисерам у створенні фільмів та серіалів',
            'producer_assistant' => 'Помічники продюсера, які допомагають продюсерам у створенні фільмів та серіалів',
            'script_supervisor' => 'Супервайзери сценарію, які контролюють дотримання сценарію у фільмах та серіалах',
            'production_designer' => 'Художники-постановники, які створюють візуальний стиль фільмів та серіалів',
            'visual_effects_supervisor' => 'Супервайзери візуальних ефектів, які створюють візуальні ефекти для фільмів та серіалів',
        ],
        'meta_image' => [
            'actor' => '/images/seo/person-actor.jpg',
            'character' => '/images/seo/person-character.jpg',
            'director' => '/images/seo/person-director.jpg',
            'producer' => '/images/seo/person-producer.jpg',
            'writer' => '/images/seo/person-writer.jpg',
            'editor' => '/images/seo/person-editor.jpg',
            'cinematographer' => '/images/seo/person-cinematographer.jpg',
            'composer' => '/images/seo/person-composer.jpg',
            'art_director' => '/images/seo/person-art-director.jpg',
            'sound_designer' => '/images/seo/person-sound-designer.jpg',
            'costume_designer' => '/images/seo/person-costume-designer.jpg',
            'makeup_artist' => '/images/seo/person-makeup-artist.jpg',
            'voice_actor' => '/images/seo/person-voice-actor.jpg',
            'stunt_performer' => '/images/seo/person-stunt-performer.jpg',
            'assistant_director' => '/images/seo/person-assistant-director.jpg',
            'producer_assistant' => '/images/seo/person-producer-assistant.jpg',
            'script_supervisor' => '/images/seo/person-script-supervisor.jpg',
            'production_designer' => '/images/seo/person-production-designer.jpg',
            'visual_effects_supervisor' => '/images/seo/person-visual-effects-supervisor.jpg',
        ],
    ],


    'kind' => [
        'movie' => 'Фільм',
        'tv_series' => 'ТВ серіал',
        'animated_movie' => 'Мультфільм',
        'animated_series' => 'Мультсеріал',

        'meta_title' => [
            'movie' => 'Фільми онлайн | Netflix',
            'tv_series' => 'ТВ серіали онлайн | Netflix',
            'animated_movie' => 'Мультфільми онлайн | Netflix',
            'animated_series' => 'Мультсеріали онлайн | Netflix',
        ],
        'meta_description' => [
            'movie' => 'Перегляньте найкращі фільми онлайн, від класики до новинок кіноіндустрії.',
            'tv_series' => 'Ознайомтеся з найкращими ТВ серіалами онлайн, від комедій до драм.',
            'animated_movie' => 'Перегляньте анімаційні фільми, що захоплюють своєю графікою та сюжетами.',
            'animated_series' => 'Дивіться мультсеріали для всіх вікових категорій онлайн.',
        ],
        'meta_image' => [
            'movie' => '/images/seo/movie.jpg',
            'tv_series' => '/images/seo/tv-series.jpg',
            'animated_movie' => '/images/seo/animated-movie.jpg',
            'animated_series' => '/images/seo/animated-series.jpg',
        ],
        'description' => [
            'movie' => 'Повнометражний фільм, який триває від 1 до кількох годин.',
            'tv_series' => 'Телекінематографічний серіал, який складається з кількох сезонів.',
            'animated_movie' => 'Мультфільм, який представляє собою анімацію у вигляді повнометражного фільму.',
            'animated_series' => 'Мультсеріал, що складається з кількох епізодів, де основна історія розгортається в анімаційному форматі.',
        ],
    ],


    'video_quality' => [
        'sd' => 'SD',
        'hd' => 'HD',
        'full_hd' => 'Full HD',
        'uhd' => 'UHD (4K)',

        'meta_title' => [
            'sd' => 'SD якість відео | Netflix',
            'hd' => 'HD якість відео | Netflix',
            'full_hd' => 'Full HD якість відео | Netflix',
            'uhd' => 'UHD (4K) якість відео | Netflix',
        ],
        'meta_description' => [
            'sd' => 'Перегляд відео в стандартній якості (SD)',
            'hd' => 'Перегляд відео у високій якості (HD)',
            'full_hd' => 'Перегляд відео у Full HD якості',
            'uhd' => 'Перегляд відео у надвисокій якості (UHD/4K)',
        ],
        'meta_image' => [
            'sd' => '/images/seo/quality-sd.jpg',
            'hd' => '/images/seo/quality-hd.jpg',
            'full_hd' => '/images/seo/quality-full-hd.jpg',
            'uhd' => '/images/seo/quality-uhd.jpg',
        ],
    ],


    'video_player_name' => [
        'kodik' => 'Kodik',
        'aloha' => 'Aloha',

        'meta_title' => [
            'kodik' => 'Kodik відеоплеєр | Netflix',
            'aloha' => 'Aloha відеоплеєр | Netflix',
        ],
        'meta_description' => [
            'kodik' => 'Перегляд відео через Kodik відеоплеєр',
            'aloha' => 'Перегляд відео через Aloha відеоплеєр',
        ],
        'meta_image' => [
            'kodik' => '/images/seo/player-kodik.jpg',
            'aloha' => '/images/seo/player-aloha.jpg',
        ],
    ],


    'attachment_type' => [
        'trailer' => 'Трейлер',
        'teaser' => 'Тизер',
        'behind_the_scenes' => 'За лаштунками',
        'interview' => 'Інтерв\'ю',
        'clip' => 'Кліп',
        'deleted_scene' => 'Видалена сцена',
        'blooper' => 'Невдалі дублі',
        'featurette' => 'Фічуретка',
        'picture' => 'Зображення',
    ],


    'api_source_name' => [
        'tmdb' => 'TMDB',
        'shiki' => 'Shikimori',
        'imdb' => 'IMDB',
        'anilist' => 'AniList',

        'meta_title' => [
            'tmdb' => 'TMDB API | Netflix',
            'shiki' => 'Shikimori API | Netflix',
            'imdb' => 'IMDB API | Netflix',
            'anilist' => 'AniList API | Netflix',
        ],
        'meta_description' => [
            'tmdb' => 'Дані з The Movie Database API',
            'shiki' => 'Дані з Shikimori API',
            'imdb' => 'Дані з Internet Movie Database API',
            'anilist' => 'Дані з AniList API',
        ],
        'meta_image' => [
            'tmdb' => '/images/seo/api-tmdb.jpg',
            'shiki' => '/images/seo/api-shiki.jpg',
            'imdb' => '/images/seo/api-imdb.jpg',
            'anilist' => '/images/seo/api-anilist.jpg',
        ],
    ],


    'movie_relate_type' => [
        'season' => 'Сезон',
        'source' => 'Джерело',
        'sequel' => 'Продовження',
        'side_story' => 'Побічна історія',
        'summary' => 'Підсумок',
        'other' => 'Інше',
        'adaptation' => 'Адаптація',
        'alternative' => 'Альтернатива',
        'prequel' => 'Передісторія',

        'meta_title' => [
            'season' => 'Сезони | Netflix',
            'source' => 'Джерела | Netflix',
            'sequel' => 'Продовження | Netflix',
            'side_story' => 'Побічні історії | Netflix',
            'summary' => 'Підсумки | Netflix',
            'other' => 'Інші зв\'язки | Netflix',
            'adaptation' => 'Адаптації | Netflix',
            'alternative' => 'Альтернативи | Netflix',
            'prequel' => 'Передісторії | Netflix',
        ],
        'meta_description' => [
            'season' => 'Сезони фільмів та серіалів',
            'source' => 'Джерела для фільмів та серіалів',
            'sequel' => 'Продовження фільмів та серіалів',
            'side_story' => 'Побічні історії фільмів та серіалів',
            'summary' => 'Підсумки фільмів та серіалів',
            'other' => 'Інші зв\'язки фільмів та серіалів',
            'adaptation' => 'Адаптації фільмів та серіалів',
            'alternative' => 'Альтернативні версії фільмів та серіалів',
            'prequel' => 'Передісторії фільмів та серіалів',
        ],
        'meta_image' => [
            'season' => '/images/seo/relate-season.jpg',
            'source' => '/images/seo/relate-source.jpg',
            'sequel' => '/images/seo/relate-sequel.jpg',
            'side_story' => '/images/seo/relate-side-story.jpg',
            'summary' => '/images/seo/relate-summary.jpg',
            'other' => '/images/seo/relate-other.jpg',
            'adaptation' => '/images/seo/relate-adaptation.jpg',
            'alternative' => '/images/seo/relate-alternative.jpg',
            'prequel' => '/images/seo/relate-prequel.jpg',
        ],
    ],


    'comment_report' => [
        'insult' => 'Образа',
        'flood_offtop_meaningless' => 'Флуд/Оффтоп/Беззмістовність',
        'ad_spam' => 'Реклама/Спам',
        'spoiler' => 'Спойлер',
        'provocation_conflict' => 'Провокація/Конфлікт',
        'inappropriate_language' => 'Ненормативна лексика',
        'forbidden_unnecessary_content' => 'Заборонений/Непотрібний контент',
        'meaningless_empty_topic' => 'Беззмістовна/Порожня тема',
        'duplicate_topic' => 'Дублікат теми',

        'meta_title' => [
            'insult' => 'Скарги на образи | Netflix',
            'flood_offtop_meaningless' => 'Скарги на флуд/оффтоп | Netflix',
            'ad_spam' => 'Скарги на рекламу/спам | Netflix',
            'spoiler' => 'Скарги на спойлери | Netflix',
            'provocation_conflict' => 'Скарги на провокації | Netflix',
            'inappropriate_language' => 'Скарги на ненормативну лексику | Netflix',
            'forbidden_unnecessary_content' => 'Скарги на заборонений контент | Netflix',
            'meaningless_empty_topic' => 'Скарги на беззмістовні теми | Netflix',
            'duplicate_topic' => 'Скарги на дублікати тем | Netflix',
        ],
        'meta_description' => [
            'insult' => 'Скарги на образливі коментарі',
            'flood_offtop_meaningless' => 'Скарги на флуд, оффтоп та беззмістовні коментарі',
            'ad_spam' => 'Скарги на рекламу та спам у коментарях',
            'spoiler' => 'Скарги на спойлери у коментарях',
            'provocation_conflict' => 'Скарги на провокації та конфлікти у коментарях',
            'inappropriate_language' => 'Скарги на ненормативну лексику у коментарях',
            'forbidden_unnecessary_content' => 'Скарги на заборонений та непотрібний контент у коментарях',
            'meaningless_empty_topic' => 'Скарги на беззмістовні та порожні теми',
            'duplicate_topic' => 'Скарги на дублікати тем',
        ],
        'meta_image' => [
            'insult' => '/images/seo/report-insult.jpg',
            'flood_offtop_meaningless' => '/images/seo/report-flood.jpg',
            'ad_spam' => '/images/seo/report-spam.jpg',
            'spoiler' => '/images/seo/report-spoiler.jpg',
            'provocation_conflict' => '/images/seo/report-provocation.jpg',
            'inappropriate_language' => '/images/seo/report-language.jpg',
            'forbidden_unnecessary_content' => '/images/seo/report-forbidden.jpg',
            'meaningless_empty_topic' => '/images/seo/report-meaningless.jpg',
            'duplicate_topic' => '/images/seo/report-duplicate.jpg',
        ],
    ],
];
