<?php

namespace App\DTOs\CommentReports;

use App\DTOs\BaseDTO;
use App\Enums\CommentReportType;
use Illuminate\Http\Request;

class CommentReportUpdateDTO extends BaseDTO
{
    /**
     * Create a new CommentReportUpdateDTO instance.
     *
     * @param bool|null $isViewed Whether the report has been viewed
     * @param CommentReportType|null $type Report type
     * @param string|null $body Report description
     */
    public function __construct(
        public readonly ?bool $isViewed = null,
        public readonly ?CommentReportType $type = null,
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
            'is_viewed' => 'isViewed',
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
        $type = null;
        if ($request->has('type') && $request->input('type')) {
            try {
                $type = CommentReportType::from($request->input('type'));
            } catch (\ValueError $e) {
                // Invalid type, ignore
            }
        }

        return new static(
            isViewed: $request->has('is_viewed') ? $request->boolean('is_viewed') : null,
            type: $type,
            body: $request->has('body') ? $request->input('body') : null,
        );
    }
}
