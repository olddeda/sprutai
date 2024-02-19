<?php
namespace api\models\content\swagger;

/**
 *
 * @OA\RequestBody(
 *     request="YoutubeInfo",
 *     required=true,
 *     description="Данные запроса",
 *     @OA\JsonContent(
 *         @OA\Property(property="url", type="string", description="Ссылка на Youtube ролик", example="https://www.youtube.com/watch?v=sAoc6vv3Fys&t=45s")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="kind", type="string"),
 *     @OA\Property(property="etag", type="string"),
 *     @OA\Property(property="id", type="string"),
 *     @OA\Property(property="snippet", type="object",
 *         @OA\Property(property="publishedAt", type="string"),
 *         @OA\Property(property="channelId", type="string"),
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(property="description", type="string"),
 *         @OA\Property(property="thumbnails", type="object",
 *             @OA\Property(property="default", type="object",
 *                @OA\Property(property="url", type="string"),
 *                @OA\Property(property="width", type="integer"),
 *                @OA\Property(property="height", type="integer")
 *             ),
 *             @OA\Property(property="medium", type="object",
 *                @OA\Property(property="url", type="string"),
 *                @OA\Property(property="width", type="integer"),
 *                @OA\Property(property="height", type="integer")
 *             ),
 *             @OA\Property(property="high", type="object",
 *                @OA\Property(property="url", type="string"),
 *                @OA\Property(property="width", type="integer"),
 *                @OA\Property(property="height", type="integer")
 *             ),
 *             @OA\Property(property="standard", type="object",
 *                @OA\Property(property="url", type="string"),
 *                @OA\Property(property="width", type="integer"),
 *                @OA\Property(property="height", type="integer")
 *             ),
 *             @OA\Property(property="maxres", type="object",
 *                @OA\Property(property="url", type="string"),
 *                @OA\Property(property="width", type="integer"),
 *                @OA\Property(property="height", type="integer")
 *             )
 *         ),
 *         @OA\Property(property="channelTitle", type="string"),
 *         @OA\Property(property="tags", type="array", @OA\Items(type="string")),
 *         @OA\Property(property="liveBroadcastContent", type="string"),
 *         @OA\Property(property="defaultAudioLanguage", type="string"),
 *         @OA\Property(property="localized", type="string",
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Property(property="contentDetails", type="object",
 *         @OA\Property(property="duration", type="string"),
 *         @OA\Property(property="dimension", type="string"),
 *         @OA\Property(property="definition", type="string"),
 *         @OA\Property(property="caption", type="boolean"),
 *         @OA\Property(property="licensedContent", type="boolean"),
 *         @OA\Property(property="contentRating", type="object"),
 *         @OA\Property(property="projection", type="string"),
 *     ),
 *     @OA\Property(property="status", type="object",
 *         @OA\Property(property="uploadStatus", type="string"),
 *         @OA\Property(property="privacyStatus", type="string"),
 *         @OA\Property(property="license", type="string"),
 *         @OA\Property(property="embeddable", type="boolean"),
 *         @OA\Property(property="publicStatsViewable", type="boolean"),
 *         @OA\Property(property="madeForKids", type="boolean")
 *     ),
 *     @OA\Property(property="statistics", type="object",
 *         @OA\Property(property="viewCount", type="integer"),
 *         @OA\Property(property="likeCount", type="integer"),
 *         @OA\Property(property="dislikeCount", type="integer"),
 *         @OA\Property(property="favoriteCount", type="integer"),
 *         @OA\Property(property="commentCount", type="integer")
 *     ),
 *     @OA\Property(property="player", type="object",
 *         @OA\Property(property="embedHtml", type="string")
 *     )
 *
 * )
 */
class YoutubeInfo {}