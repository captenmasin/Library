<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
