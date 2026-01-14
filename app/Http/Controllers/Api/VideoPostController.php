<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\VideoPostResource;
use App\Models\VideoPost;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VideoPostController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $videoPosts = VideoPost::orderBy('created_at', 'desc')->paginate(15);
        return VideoPostResource::collection($videoPosts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $videoPost = VideoPost::create($validated);

        return (new VideoPostResource($videoPost))
            ->response()
            ->setStatusCode(201);
    }

    public function show(VideoPost $videoPost, Request $request): JsonResponse
    {
        $comments = $this->commentService->getCommentsForModel(
            $videoPost,
            $request->integer('per_page', 15)
        );

        return response()->json([
            'data' => new VideoPostResource($videoPost),
            'comments' => CommentResource::collection($comments)->response()->getData(true),
        ]);
    }
}
