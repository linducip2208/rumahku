<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query()->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->string('city')->toString().'%');
        }

        return view('pages.services-index', [
            'services' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['category', 'city']),
        ]);
    }
}
