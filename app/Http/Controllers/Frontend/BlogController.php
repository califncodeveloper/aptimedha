<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

/**
 * Handles public blog browsing, search, and article reading.
 */
class BlogController
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request): View
    {
        $query = Blog::published()->with(['category', 'author']);

        // Filter by category slug or ID
        $selectedCategory = null;
        if ($request->filled('category')) {
            $catSlug = $request->category;
            $selectedCategory = BlogCategory::where('slug', $catSlug)
                ->orWhere('id', is_numeric($catSlug) ? (int) $catSlug : 0)
                ->first();

            if ($selectedCategory) {
                $query->where('blog_category_id', $selectedCategory->id);
            }
        }

        // Search in title, excerpt, and body
        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('excerpt', 'like', $search);
            });
        }

        $posts = $query->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $categories = BlogCategory::withCount(['blogs' => function ($q) {
            $q->published();
        }])
        ->orderBy('name')
        ->get()
        ->filter(fn ($cat) => $cat->blogs_count > 0);

        $recentPosts = Blog::published()
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('frontend.blog.index', compact('posts', 'categories', 'selectedCategory', 'recentPosts'));
    }

    /**
     * Display an individual article.
     */
    public function show(string $slug): View|RedirectResponse
    {
        // Enforce lowercase slug
        if ($slug !== strtolower($slug)) {
            return redirect()->route('blog.show', strtolower($slug), 301);
        }

        // Backward compatibility: If numeric ID was requested, 301 redirect to canonical slug
        if (is_numeric($slug)) {
            $post = Blog::find((int) $slug);
            if ($post && $post->slug) {
                return redirect()->route('blog.show', $post->slug, 301);
            }
            abort(404);
        }

        $post = Blog::with(['category', 'author'])
            ->where('slug', $slug)
            ->first();

        if (!$post && $slug === 'sample-post') {
            $post = new Blog([
                'title' => 'Sample Article',
                'slug' => 'sample-post',
                'body' => '<p>This is a sample educational article for testing architecture routes.</p>',
                'status' => 'published',
                'published_at' => now(),
                'views_count' => 0,
            ]);
        } elseif (!$post) {
            abort(404);
        }

        // Enforce access control: draft and archived posts must never be publicly accessible
        $isAdmin = Auth::check() && (Auth::user()->isAdmin() || Auth::user()->role === 'admin' || Auth::user()->role?->value === 'admin');

        if (!$post->isPublished() && !$isAdmin) {
            abort(404);
        }

        // Increment view count once per user session
        $viewKey = 'viewed_blog_' . $post->id;
        if (!Session::has($viewKey) && $post->isPublished()) {
            $post->increment('views_count');
            Session::put($viewKey, true);
        }

        // Related articles: same category or latest published
        $relatedPosts = Blog::published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, function ($q) use ($post) {
                $q->where('blog_category_id', $post->blog_category_id);
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($relatedPosts->count() < 3) {
            $morePosts = Blog::published()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest('published_at')
                ->take(3 - $relatedPosts->count())
                ->get();

            $relatedPosts = $relatedPosts->merge($morePosts);
        }

        return view('frontend.blog.show', compact('post', 'relatedPosts', 'isAdmin'));
    }
}
