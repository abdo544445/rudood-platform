<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $articles = $query->latest()->paginate(10)->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'read_time'    => 'required|string|max:50',
            'summary'      => 'required|string',
            'content'      => 'required|string',
            'icon'         => 'nullable|string|max:50',
            'is_featured'  => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->title);
        if (Article::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $is_published = $request->boolean('is_published');

        Article::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'category'     => $request->category,
            'read_time'    => $request->read_time,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'icon'         => $request->icon ?? 'bi-journal-text',
            'is_featured'  => $request->boolean('is_featured'),
            'is_published' => $is_published,
            'published_at' => $is_published ? now() : null,
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'تم إنشاء المقال بنجاح.');
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'read_time'    => 'required|string|max:50',
            'summary'      => 'required|string',
            'content'      => 'required|string',
            'icon'         => 'nullable|string|max:50',
            'is_featured'  => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $slug = Str::slug($request->title);
        if (Article::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug .= '-' . time();
        }

        $was_published = $article->is_published;
        $is_published = $request->boolean('is_published');

        $article->update([
            'title'        => $request->title,
            'slug'         => $slug,
            'category'     => $request->category,
            'read_time'    => $request->read_time,
            'summary'      => $request->summary,
            'content'      => $request->content,
            'icon'         => $request->icon ?? 'bi-journal-text',
            'is_featured'  => $request->boolean('is_featured'),
            'is_published' => $is_published,
            'published_at' => ($is_published && !$was_published) ? now() : $article->published_at,
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'تم تحديث المقال بنجاح.');
    }

    public function togglePublish($id)
    {
        $article = Article::findOrFail($id);
        $new_status = !$article->is_published;

        $article->update([
            'is_published' => $new_status,
            'published_at' => $new_status ? now() : $article->published_at,
        ]);

        $msg = $new_status ? 'تم نشر المقال بنجاح.' : 'تم تحويل المقال إلى مسودة.';
        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return back()->with('success', 'تم حذف المقال بنجاح.');
    }
}
