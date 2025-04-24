<?php

namespace App\Http\Controllers;

use App\Actions\Enums\FormatEnum;
use App\DTOs\Enums\EnumDTO;
use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\CommentReportType;
use App\Enums\Gender;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\PaymentStatus;
use App\Enums\PersonType;
use App\Enums\Status;
use App\Enums\UserListType;
use App\Enums\VideoQuality;
use App\Http\Requests\Enums\EnumRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class EnumController extends Controller
{
    /**
     * Get all movie kinds with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function kinds(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $kinds = [];

        foreach (Kind::cases() as $kind) {
            $kinds[] = $action->handle('kind', $kind, $dto);
        }

        return response()->json($kinds);
    }

    /**
     * Get specific movie kind with SEO data
     *
     * @param string $kind
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function kind(string $kind, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $kindEnum = Kind::from($kind);

            return response()->json($action->handle('kind', $kindEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid kind value'], 404);
        }
    }

    /**
     * Get all movie statuses with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function statuses(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $statuses = [];

        foreach (Status::cases() as $status) {
            $statuses[] = $action->handle('status', $status, $dto);
        }

        return response()->json($statuses);
    }

    /**
     * Get specific movie status with SEO data
     *
     * @param string $status
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function status(string $status, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $statusEnum = Status::from($status);

            return response()->json($action->handle('status', $statusEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid status value'], 404);
        }
    }

    /**
     * Get all video qualities with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function videoQualities(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $qualities = [];

        foreach (VideoQuality::cases() as $quality) {
            $qualities[] = $action->handle('video_quality', $quality, $dto);
        }

        return response()->json($qualities);
    }

    /**
     * Get specific video quality with SEO data
     *
     * @param string $quality
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function videoQuality(string $quality, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $qualityEnum = VideoQuality::from($quality);

            return response()->json($action->handle('video_quality', $qualityEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid video quality value'], 404);
        }
    }

    /**
     * Get all person types with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function personTypes(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $types = [];

        foreach (PersonType::cases() as $type) {
            $types[] = $action->handle('person_type', $type, $dto);
        }

        return response()->json($types);
    }

    /**
     * Get specific person type with SEO data
     *
     * @param string $type
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function personType(string $type, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $typeEnum = PersonType::from($type);

            return response()->json($action->handle('person_type', $typeEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid person type value'], 404);
        }
    }

    /**
     * Get all user list types with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function userListTypes(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $types = [];

        foreach (UserListType::cases() as $type) {
            $types[] = $action->handle('user_list_type', $type, $dto);
        }

        return response()->json($types);
    }

    /**
     * Get specific user list type with SEO data
     *
     * @param string $type
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function userListType(string $type, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $typeEnum = UserListType::from($type);

            return response()->json($action->handle('user_list_type', $typeEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid user list type value'], 404);
        }
    }

    /**
     * Get all genders with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function genders(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $genders = [];

        foreach (Gender::cases() as $gender) {
            $genders[] = $action->handle('gender', $gender, $dto);
        }

        return response()->json($genders);
    }

    /**
     * Get specific gender with SEO data
     *
     * @param string $gender
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function gender(string $gender, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $genderEnum = Gender::from($gender);

            return response()->json($action->handle('gender', $genderEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid gender value'], 404);
        }
    }

    /**
     * Get all comment report types with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function commentReportTypes(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $types = [];

        foreach (CommentReportType::cases() as $type) {
            $types[] = $action->handle('comment_report', $type, $dto);
        }

        return response()->json($types);
    }

    /**
     * Get specific comment report type with SEO data
     *
     * @param string $type
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function commentReportType(string $type, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $typeEnum = CommentReportType::from($type);

            return response()->json($action->handle('comment_report', $typeEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid comment report type value'], 404);
        }
    }

    /**
     * Get all movie relate types with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function movieRelateTypes(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $types = [];

        foreach (MovieRelateType::cases() as $type) {
            $types[] = $action->handle('movie_relate_type', $type, $dto);
        }

        return response()->json($types);
    }

    /**
     * Get specific movie relate type with SEO data
     *
     * @param string $type
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function movieRelateType(string $type, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $typeEnum = MovieRelateType::from($type);

            return response()->json($action->handle('movie_relate_type', $typeEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid movie relate type value'], 404);
        }
    }

    /**
     * Get all payment statuses with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function paymentStatuses(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $statuses = [];

        foreach (PaymentStatus::cases() as $status) {
            $statuses[] = $action->handle('payment_status', $status, $dto);
        }

        return response()->json($statuses);
    }

    /**
     * Get specific payment status with SEO data
     *
     * @param string $status
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function paymentStatus(string $status, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $statusEnum = PaymentStatus::from($status);

            return response()->json($action->handle('payment_status', $statusEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid payment status value'], 404);
        }
    }

    /**
     * Get all API source names with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function apiSourceNames(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $sources = [];

        foreach (ApiSourceName::cases() as $source) {
            $sources[] = $action->handle('api_source_name', $source, $dto);
        }

        return response()->json($sources);
    }

    /**
     * Get specific API source name with SEO data
     *
     * @param string $source
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function apiSourceName(string $source, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $sourceEnum = ApiSourceName::from($source);

            return response()->json($action->handle('api_source_name', $sourceEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid API source name value'], 404);
        }
    }

    /**
     * Get all attachment types with SEO data
     *
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function attachmentTypes(EnumRequest $request, FormatEnum $action): JsonResponse
    {
        $dto = EnumDTO::fromRequest($request);
        $types = [];

        foreach (AttachmentType::cases() as $type) {
            $types[] = $action->handle('attachment_type', $type, $dto);
        }

        return response()->json($types);
    }

    /**
     * Get specific attachment type with SEO data
     *
     * @param string $type
     * @param EnumRequest $request
     * @param FormatEnum $action
     * @return JsonResponse
     */
    public function attachmentType(string $type, EnumRequest $request, FormatEnum $action): JsonResponse
    {
        try {
            $dto = EnumDTO::fromRequest($request);
            $typeEnum = AttachmentType::from($type);

            return response()->json($action->handle('attachment_type', $typeEnum, $dto));
        } catch (\ValueError $e) {
            return response()->json(['error' => 'Invalid attachment type value'], 404);
        }
    }
}
