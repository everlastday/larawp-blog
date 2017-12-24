<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $limit = 3;

    public function index()
    {

        //\DB::enableQueryLog();
        $posts = Post::with('author')
            ->latestFirst()
            ->published()
            ->simplePaginate($this->limit);
        return view("blog.index", compact('posts'));
        //dd(\DB::getQueryLog());
    }

    public function show(Post $post)
    {
        //$post = Post::published()->findOrFail($id);
        return view("blog.show", compact('post'));
    }
}
