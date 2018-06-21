<?php

namespace App\Http\Controllers\Backend;

use App\Post;
use App\Http\Requests;
class BlogController extends Controller
{
    protected $limit = 5;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $posts = Post::with('category', 'author')
            ->latest()
            ->paginate($this->limit);
        $postCount = Post::count();

        return view("backend.blog.index", compact('posts', 'postCount'));
    }

    /**
     * Show the form creating a new resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function create(Post $post)
    {
        return view('backend.blog.create', compact('post'));
    }


    public function store(Requests\PostRequest $request)
    {

        $request->user()->posts()->create($request->all());

        return redirect('/backend/blog')->with('message', 'Your post was created successfully!');
    }

}
