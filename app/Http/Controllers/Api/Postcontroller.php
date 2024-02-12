<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class Postcontroller extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return new PostResource(true, 'List Data Posts' , $posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil di tambahkan!', $post);
    }

    public function show($id)
    {
        $post = Post::find($id);
        return new PostResource(true, 'detail data post!', $post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        return new PostResource(true, 'Data Post Berhasil di hapus!', $post);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil di ubah!', $post);
    }

    
}
