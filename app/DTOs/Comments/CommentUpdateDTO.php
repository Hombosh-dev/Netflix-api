<?php

namespace App\DTOs\Comments;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class CommentUpdateDTO extends BaseDTO
{
    /**
     * Create a new CommentUpdateDTO instance.
     *
     * @param string $body Comment body
     * @param bool|null $isSpoiler Whether the comment contains spoilers
     */
    public function __construct(
        public readonly string $body,
        public readonly ?bool $isSpoiler = null,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'body',
            'is_spoiler' => 'isSpoiler',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            body: $request->input('body'),
            isSpoiler: $request->has('is_spoiler') ? $request->boolean('is_spoiler') : null,
        );
    }
}
