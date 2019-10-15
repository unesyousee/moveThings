<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::paginate(15);
        return view('admin/blog/post/index', compact('posts'));
    }

    public function create()
    {
        return view('admin/blog/post/create');
    }

    public function store(Request $request)
    {
        $post=new Post;
        $post->title = $request->title;
        $post->body = $request->body;
        $post->abstract = $request->abstract;
        $post->author = $request->author;
        $post->status = 1;
        $photoName1 = time().'.'.$request->first_image->getClientOriginalExtension();
        $first_image = $request->first_image->move(public_path('images/post/'), $photoName1);
        $post->first_image = env('APP_URL').('/images/post/').$photoName1;
        if ($request->has('second_image') ) {
            $photoName2 = time().'.'.$request->second_image->getClientOriginalExtension();
            $second_image = $request->second_image->move(public_path('images/post/'), $photoName2);
            $post->second_image = env('APP_URL').('/images/post/').$photoName1;
        }
        $post->save();
        return back();
    }

    public function show($id)
    {
        //
    }

    public function SinglePost($id)
    {
        $post = Post::find($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        if ($request->disable){
            $post=Post::find($request->id);
            $post->status=0;
            $post->save();
            return back();
        }else if ($request->enable){
            $post=Post::find($request->id);
            $post->status=1;
            $post->save();
            return back();
        }
        else {
            $post = Post::find($id);
            $post->title = $request->title;
            $post->abstract = $request->abstract;
            $post->body = $request->body;
            $post->author = $request->author;
            $post->save();
            return back();
        }
    }
    public function destroy($id)
    {
        Post::find($id)->delete();
        return back();
    }
    public function allPosts(){
        $posts = Post::where('status', 1)->orderBy('id','decd')->get();
        return response()->json($posts);
    }
}
