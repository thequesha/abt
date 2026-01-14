<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory, HasComments;

    protected $fillable = [
        'title',
        'description',
    ];
}
