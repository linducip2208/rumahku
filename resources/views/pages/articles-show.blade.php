@extends('layouts.metronic')

@section('title', ($article->title ?? 'Artikel') . ' — Rumah123')

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-8">
    <nav class="text-xs text-muted-foreground mb-2">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('articles.index') }}" class="hover:underline">Artikel</a> /
        <span>{{ \Illuminate\Support\Str::limit($article->title, 50) }}</span>
    </nav>
    <div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ $article->type ?? 'Artikel' }}{{ $article->city ? ' · ' . $article->city : '' }}</div>
    <h1 class="text-2xl lg:text-3xl font-extrabold mt-1">{{ $article->title }}</h1>
    <div class="kt-card p-5 mt-4">
        <div class="text-sm leading-relaxed whitespace-pre-line">{{ $article->body }}</div>
    </div>

    @if (! empty($related ?? null) && $related->isNotEmpty())
        <h2 class="font-bold mt-8 mb-3">Artikel Terkait</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($related as $rel)
                <a href="{{ route('articles.show', $rel->slug) }}" class="kt-card p-4 hover:shadow-md transition">
                    <div class="font-semibold text-sm line-clamp-2">{{ $rel->title }}</div>
                    <div class="text-xs text-muted-foreground mt-1">{{ $rel->city ?? '' }}</div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
