<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivitiesController extends Controller
{
    public function index(Request $request): InertiaResponse|AnonymousResourceCollection
    {
        $activities = $request->user()->activities()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return ActivityResource::collection($activities);
        }

        return Inertia::render('user/Activities', [
            'activities' => ActivityResource::collection($activities),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Activities', 'href' => route('user.activities.index')],
            ],
        ])->withMeta([
            'title' => 'Activities',
            'description' => 'A list of your recent activities.',
        ]);
    }
}
