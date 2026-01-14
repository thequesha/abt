<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'commentable_type' => 'required_without:parent_id|string',
            'commentable_id' => 'required_without:parent_id|integer',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if (isset($validated['parent_id'])) {
            $parentComment = Comment::findOrFail($validated['parent_id']);
            $validated['commentable_type'] = $parentComment->commentable_type;
            $validated['commentable_id'] = $parentComment->commentable_id;
        } else {
            $commentableType = $this->commentService->resolveCommentableType($validated['commentable_type']);

            if (!$commentableType) {
                throw ValidationException::withMessages([
                    'commentable_type' => 'Invalid commentable type. Use "news" or "video".',
                ]);
            }

            $validated['commentable_type'] = $commentableType;
        }

        $comment = $this->commentService->create($validated);
        $comment->load(['user', 'replies.user']);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Comment $comment): CommentResource
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $updatedComment = $this->commentService->update($comment, $validated);
        $updatedComment->load(['user', 'replies.user']);

        return new CommentResource($updatedComment);
    }

    public function destroy(Comment $comment): Response
    {
        $this->commentService->delete($comment);

        return response()->noContent();
    }
}
