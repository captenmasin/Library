<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function posts(Request $request, User $user)
    {
        return $user->posts()->get();
    }
}
