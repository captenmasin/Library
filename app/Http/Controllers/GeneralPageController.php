<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class GeneralPageController extends Controller
{
    public function privacy()
    {
        return Inertia::render('PrivacyPolicy')
            ->withMeta([
                'title' => 'Privacy Policy',
                'description' => 'Read our privacy policy to understand how we handle your data.',
            ]);
    }
}
