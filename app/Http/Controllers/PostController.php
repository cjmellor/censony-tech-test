<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Post;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['author', 'category'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return Inertia::render('Posts/Index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Posts/Create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        abort_if($request->user()->cannot('create', Post::class), 403);

        // Create a new post with the validated data
        $post = new Post($request->validated());

        $post->user_id = auth()->id();
        $post->published_at = now();
        $post->save();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'category']);

        return Inertia::render('Posts/Show', [
            'post' => $post,
            // Pass authorisation information to the frontend
            'can' => [
                'edit' => auth()->check() && auth()->user()->can('update', $post),
                'delete' => auth()->check() && auth()->user()->can('delete', $post),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        abort_if(auth()->user()->cannot('update', $post), 403);

        return Inertia::render('Posts/Edit', [
            'post' => $post->load(['category']),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        abort_if($request->user()->cannot('update', $post), 403);

        $post->update($request->validated());

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if(auth()->user()->cannot('delete', $post), 403);

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
