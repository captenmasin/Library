<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class UserController extends Controller
{
    public function posts(Request $request, User $user)
    {
        $posts = $request->user()->posts()->with(['user', 'book'])->latest()->get();

        return Inertia::render('posts/Index', [
            'posts' => PostResource::collection($posts),
        ]);
    }
}
