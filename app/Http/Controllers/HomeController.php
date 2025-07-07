<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Enums\UserBookStatus;
use App\Http\Resources\BookResource;
use App\Http\Resources\ActivityResource;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Home', [
            'stats' => [
                'books' => 0,
                'completedBooks' => 0,
                'readingBooks' => 0,
            ],
            'currentlyReading' => BookResource::collection(
                $request->user()->books()
                    ->wherePivot('status', UserBookStatus::Reading)
                    ->get()
            ),
            'activities' => ActivityResource::collection($request->user()->activities()->get()),
        ]);
    }
}
