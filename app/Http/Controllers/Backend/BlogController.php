<?php

namespace App\Http\Controllers\Backend;

use App\Post;
use Illuminate\Http\Request;

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
        return view("backend.blog.index", compact('posts'));
    }

    /**
     * Show the form creating a new resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function create()
    {
        dd('create new blog post');
    }
}
