<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:50'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Article::query()->orderByDesc('id');

        if (! empty($validated['city'])) {
            $query->where('city', $validated['city']);
        }

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (! empty($validated['q'])) {
            $query->where('title', 'like', '%'.$validated['q'].'%');
        }

        return ArticleResource::collection($query->paginate(15)->withQueryString());
    }

    public function show(string $slug)
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();

        return new ArticleResource($article);
    }
}
