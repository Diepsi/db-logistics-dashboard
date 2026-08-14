<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * CRUD Berita/Artikel untuk pengelolaan website (CMS).
 * Hanya untuk role admin, diakses melalui jalur login /website/login.
 */
class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with('author')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('website.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('website.posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Post::create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'cover_image' => $data['cover_image'] ?? null,
            'author_id' => $request->user()->id,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return redirect()->route('website.posts.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function edit(Post $post): View
    {
        return view('website.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request);

        $post->update([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title'], $post->id),
            'excerpt' => $data['excerpt'],
            'body' => $data['body'],
            'cover_image' => $data['cover_image'] ?? $post->cover_image,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        return redirect()->route('website.posts.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_image !== null) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return redirect()->route('website.posts.index')->with('success', 'Berita berhasil dihapus.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        return $data;
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'berita';
        $slug = $base;
        $suffix = 2;

        while (Post::where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
