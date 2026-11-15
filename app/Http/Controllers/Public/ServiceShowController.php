<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Contracts\View\View;

class ServiceShowController extends Controller
{
    public function show(Service $service): View
    {
        return view('pages.services-show', [
            'service' => $service,
        ]);
    }
}
