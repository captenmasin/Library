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
                'booksInLibrary' => $request->user()->books()->count(),
                'completedBooks' => $request->user()->books()
                    ->wherePivot('status', UserBookStatus::Completed)
                    ->count(),
                'planToRead' => $request->user()->books()
                    ->wherePivot('status', UserBookStatus::PlanToRead)
                    ->count(),
            ],
            'currentlyReading' => BookResource::collection(
                $request->user()->books()
                    ->with(['covers', 'authors'])
                    ->wherePivot('status', UserBookStatus::Reading)
                    ->get()
            ),
            'activities' => ActivityResource::collection($request->user()->activities()->orderByDesc('id')->get()),
        ]);
    }
}
