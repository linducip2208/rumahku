<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->latest()
            ->paginate(12);

        return view('pages.articles-index', [
            'articles' => $articles,
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();

        $related = Article::query()
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(4)
            ->get();

        return view('pages.articles-show', [
            'article' => $article,
            'related' => $related,
        ]);
    }
}
