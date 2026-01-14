<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $news = News::orderBy('created_at', 'desc')->paginate(15);
        return NewsResource::collection($news);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $news = News::create($validated);

        return (new NewsResource($news))
            ->response()
            ->setStatusCode(201);
    }

    public function show(News $news, Request $request): JsonResponse
    {
        $comments = $this->commentService->getCommentsForModel(
            $news,
            $request->integer('per_page', 15)
        );

        return response()->json([
            'data' => new NewsResource($news),
            'comments' => CommentResource::collection($comments)->response()->getData(true),
        ]);
    }
}
