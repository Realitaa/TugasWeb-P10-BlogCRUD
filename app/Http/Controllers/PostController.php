<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $posts = Post::withTrashed()
            ->with('media')
            ->when($request->query('search'), function ($query, string $search): void {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Str::of((string) $value)->wordCount() > 64) {
                        $fail('Judul maksimal 64 kata.');
                    }
                },
            ],
            'body' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Str::of((string) $value)->wordCount() > 1000) {
                        $fail('Konten body maksimal 1000 kata.');
                    }
                },
            ],
            'media' => ['nullable', 'array', 'max:3'],
            'media.*' => ['image', 'max:512'],
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
                $storedFileName = (string) Str::uuid().'.'.strtolower($extension);
                $path = $file->storeAs('media', $storedFileName, 'public');

                $post->media()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? 'image/jpeg',
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        $post->load('media');

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        $post->load('media');

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Str::of((string) $value)->wordCount() > 64) {
                        $fail('Judul maksimal 64 kata.');
                    }
                },
            ],
            'body' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Str::of((string) $value)->wordCount() > 1000) {
                        $fail('Konten body maksimal 1000 kata.');
                    }
                },
            ],
            'media' => ['nullable', 'array', 'max:3'],
            'media.*' => ['image', 'max:512'],
            'delete_media' => ['nullable', 'array'],
            'delete_media.*' => ['integer'],
        ]);

        if ($request->filled('delete_media')) {
            $mediaToDelete = $post->media()->whereIn('id', $request->input('delete_media'))->get();
            foreach ($mediaToDelete as $media) {
                $media->delete();
            }
        }

        $currentMediaCount = $post->media()->count();
        $newMediaCount = $request->hasFile('media') ? count($request->file('media')) : 0;

        if ($currentMediaCount + $newMediaCount > 3) {
            return back()->withInput()->withErrors([
                'media' => 'Total media untuk sebuah post tidak boleh melebihi 3 file.',
            ]);
        }

        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
                $storedFileName = (string) Str::uuid().'.'.strtolower($extension);
                $path = $file->storeAs('media', $storedFileName, 'public');

                $post->media()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?? 'image/jpeg',
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post berhasil dihapus.');
    }
}
