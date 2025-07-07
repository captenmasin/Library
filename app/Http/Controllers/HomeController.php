<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Home', [
            'activities' => ActivityResource::collection($request->user()->activities()->get()),
        ]);
    }
}
