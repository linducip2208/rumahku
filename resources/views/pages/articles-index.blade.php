@extends('layouts.metronic')

@section('title', 'Artikel & Panduan Properti — Rumah123')

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-8">
    <h1 class="text-2xl font-extrabold">Artikel & Panduan</h1>
    <p class="text-sm text-muted-foreground mt-1">Tips membeli, menjual, dan berinvestasi properti.</p>
    <div class="mt-6">
        @if ($articles->total() === 0)
            <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground">
                Belum ada artikel.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('articles.show', $article->slug) }}" class="kt-card p-5 hover:shadow-md transition">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">{{ $article->type ?? 'Artikel' }}{{ $article->city ? ' · ' . $article->city : '' }}</div>
                        <div class="font-bold mt-1 line-clamp-2">{{ $article->title }}</div>
                        <div class="text-sm text-muted-foreground mt-2 line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($article->body), 140) }}</div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $articles->links() }}</div>
        @endif
    </div>
</div>
@endsection
