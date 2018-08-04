<?php

namespace App\Http\Controllers\Backend;

use App\Post;
use App\Http\Requests;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{

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
        $onlyTrashed = FALSE;

        if (($status = $request->get('status')) && $status == 'trash') {
            $posts = Post::onlyTrashed()->with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::onlyTrashed()->count();
            $onlyTrashed = TRUE;

        } elseif ($status == 'published') {
            $posts = Post::published()->with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::published()->count();

        } elseif ($status == 'scheduled') {
            $posts = Post::scheduled()->with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::scheduled()->count();

        } elseif ($status == 'draft') {
            $posts = Post::draft()->with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::draft()->count();

        } else {
            $posts = Post::with('category', 'author')
                ->latest()
                ->paginate($this->limit);
            $postCount = Post::count();

        }
        $statusList = $this->statusList();
        return view("backend.blog.index", compact('posts', 'postCount', 'onlyTrashed', 'statusList'));
    }

    private function statusList()
    {
        return [
            'all' => Post::count(),
            'published' => Post::published()->count(),
            'scheduled' => Post::scheduled()->count(),
            'draft' => Post::draft()->count(),
            'trash' => Post::onlyTrashed()->count(),
        ];
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
        $oldImage = $post->image;
        $data = $this->handleRequest($request);
        $post->update($data);

        if ($oldImage != $post->image) {
            $this->removeImage($oldImage);
        }

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

        return redirect()->back()->with('message', 'Your post has been moved from the Trash ');

    }

    public function forceDestroy($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->forceDelete();

        $this->removeImage($post->image);

        return redirect('/backend/blog?status=trash')->with('message', 'Your post has been deleted successfully');
    }

    private function removeImage($image)
    {
        if (!empty($image)) {
            $imagePath = $this->uploadPath . '/' . $image;
            $ext = substr(strrchr($image, '.'), 1);
            $thumbnail = str_replace(".{$ext}", "_thumb.{$ext}", $image);
            $thumbnailPath = $this->uploadPath . '/' . $thumbnail;


            if (file_exists($imagePath)) unlink($imagePath);
            if (file_exists($thumbnailPath)) unlink($thumbnailPath);
        }
    }

}
