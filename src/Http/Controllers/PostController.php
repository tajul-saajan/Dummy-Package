<?php

namespace Tajul\Saajan\Http\Controllers;

use Tajul\Saajan\Events\PostWasCreated;
use Tajul\Saajan\Models\Post;

class PostController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('capitalize');
    }
    
    public function index()
    {
        $posts = Post::all();

        return view('dummyPkg::posts.index', compact('posts'));
    }

    public function show()
    {
        $post = Post::findOrFail(request('post'));

        return view('dummyPkg::posts.show', compact('post'));
    }

    public function store()
    {
        // Let's assume we need to be authenticated
        // to create a new post
        if (!auth()->check()) {
            abort(403, 'Only authenticated users can create new posts.');
        }

        request()->validate([
            'title' => 'required',
            'body'  => 'required',
        ]);

        // Assume the authenticated user is the post's author
        $author = auth()->user();

        $post = $author->posts()->create([
            'title'     => request('title'),
            'body'      => request('body'),
        ]);

        event(new PostWasCreated($post));

        return redirect(route('posts.show', $post));
    }
}
