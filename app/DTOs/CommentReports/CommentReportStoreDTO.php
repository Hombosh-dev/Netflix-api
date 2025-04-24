<?php

namespace App\DTOs\CommentReports;

use App\DTOs\BaseDTO;
use App\Enums\CommentReportType;
use Illuminate\Http\Request;

class CommentReportStoreDTO extends BaseDTO
{
    /**
     * Create a new CommentReportStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $commentId Comment ID
     * @param CommentReportType $type Report type
     * @param string|null $body Report description
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $commentId,
        public readonly CommentReportType $type,
        public readonly ?string $body = null,
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
            'user_id' => 'userId',
            'comment_id' => 'commentId',
            'type',
            'body',
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
            userId: $request->user()->id,
            commentId: $request->input('comment_id'),
            type: CommentReportType::from($request->input('type')),
            body: $request->input('body'),
        );
    }
}
