<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display the public blog index page.
     */
    public function index()
    {
        $featured_article = Article::published()->featured()->latest()->first();

        // If no featured article specifically flagged, pick the latest published one
        if (!$featured_article) {
            $featured_article = Article::published()->latest()->first();
        }

        $articles = Article::published()
            ->when($featured_article, fn($q) => $q->where('id', '!=', $featured_article->id))
            ->latest()
            ->get();

        return view('blog', compact('featured_article', 'articles'));
    }

    /**
     * Display an individual blog article page.
     */
    public function show($slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();

        return view('articlel', compact('article'));
    }
}
