<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the public gallery of roadmaps.
     */
    public function gallery(Request $request)
    {
        // Inertia will render the Vue component located at resources/js/Pages/Gallery.vue
        return Inertia::render('Gallery');
    }
}
