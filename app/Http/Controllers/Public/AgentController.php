<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AgentProfile;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class AgentController extends Controller
{
    public function index(): View
    {
        $agents = AgentProfile::query()
            ->with('user')
            ->whereNotNull('verified_at')
            ->latest()
            ->paginate(12);

        return view('pages.agents-index', [
            'agents' => $agents,
        ]);
    }

    public function show(int $id): View
    {
        $agent = AgentProfile::query()
            ->with('user')
            ->whereNotNull('verified_at')
            ->where('id', $id)
            ->firstOrFail();

        $properties = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->whereNull('sold_at')
            ->where('user_id', $agent->user_id)
            ->latest()
            ->paginate(12);

        return view('pages.agents-show', [
            'agent' => $agent,
            'properties' => $properties,
        ]);
    }
}
