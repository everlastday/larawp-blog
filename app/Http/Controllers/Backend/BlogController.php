<?php

namespace App\Http\Controllers\Backend;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    protected $limit = 5;
    protected $uploadPath;

    public function __construct()
    {

        parent::__construct();
        $this->uploadPath = public_path(config('cms.image.directory'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (($status = $request->get('status')) && $status == 'trash') {
            $posts = Post::onlyTrashed()->with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::onlyTrashed()->count();
            $onlyTrashed = TRUE;

        } else {
            $posts = Post::with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::count();
            $onlyTrashed = FALSE;
        }


        return view("backend.blog.index", compact('posts', 'postCount', 'onlyTrashed'));
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


    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('backend.blog.edit', compact('post'));
    }

    public function update(Requests\PostRequest $request, $id)
    {
        $post = Post::findOrFail($id);

        $data = $this->handleRequest($request);
        $post->update($data);

        return redirect('/backend/blog')->with('message', 'Your post was updated successfully!');
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


            $successUploaded = $image->move($destination, $fileName);

            if ($successUploaded) {


                $width = config('cms.image.thumbnail.width');
                $height = config('cms.image.thumbnail.height');
                $extension = $image->getClientOriginalExtension();
                $thumbnail = str_replace(".{$extension}", "_thumb.{$extension}", $fileName);
                Image::make($destination . '/' . $fileName)
                    ->resize($width, $height)
                    ->save($destination . '/' . $thumbnail);
            }


            $data['image'] = $fileName;
        }

        return $data;
    }

    public function destroy($id)
    {
        Post::findOrFail($id)->delete();

        return redirect('/backend/blog')->with('trash-message', ['Your post moved to Trash ', $id]);
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect('/backend/blog')->with('message', 'Your post has been moved from the Trash ');

    }

    public function forceDestroy($id)
    {
        Post::withTrashed()->findOrFail($id)->forceDelete();

        return redirect('/backend/blog?status=trash')->with('message', 'Your post has been deleted successfully');
    }

}
