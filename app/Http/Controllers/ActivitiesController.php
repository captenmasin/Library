<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $activities = $request->user()->activities()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('user/Activities', [
            'activities' => ActivityResource::collection($activities),
        ])->withMeta([
            'title' => 'Activities',
            'description' => 'A list of your recent activities.',
        ]);
    }
}
