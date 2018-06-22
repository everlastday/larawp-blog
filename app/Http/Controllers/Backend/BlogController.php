<?php

namespace App\Http\Controllers\Backend;

use App\Post;
use App\Http\Requests;

class BlogController extends Controller
{
    protected $limit = 5;
    protected $uploadPath;

    public function __construct()
    {

        parent::__construct();
        $this->uploadPath = public_path('img');
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

        $data = $this->handleRequest($request);


        $request->user()->posts()->create($data);

        return redirect('/backend/blog')->with('message', 'Your post was created successfully!');
    }

    public function handleRequest($request)
    {
        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = $image->getClientOriginalName();
            $destination = $this->uploadPath;
            $image->move($destination, $fileName);

            $data['image'] = $fileName;
        }

        return $data;
    }

}
