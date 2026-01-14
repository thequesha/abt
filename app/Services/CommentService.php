<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\News;
use App\Models\VideoPost;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function getCommentsForModel(Model $model, int $perPage = 15): CursorPaginator
    {
        return $model->rootComments()
            ->with([
                'user',
                'replies' => fn($query) => $query->with('user')->orderBy('created_at', 'asc'),
            ])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage);
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment->fresh();
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function resolveCommentableType(string $type): ?string
    {
        return match (strtolower($type)) {
            'news' => News::class,
            'video', 'videopost', 'video_post' => VideoPost::class,
            default => null,
        };
    }
}
