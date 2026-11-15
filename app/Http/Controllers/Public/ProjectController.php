<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->with('developer')
            ->latest()
            ->paginate(12);

        return view('pages.projects-index', [
            'projects' => $projects,
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::query()
            ->with('developer')
            ->where('slug', $slug)
            ->firstOrFail();

        $properties = Property::query()
            ->with('images')
            ->whereNotNull('published_at')
            ->where('user_id', $project->developer_user_id)
            ->latest()
            ->take(8)
            ->get();

        return view('pages.projects-show', [
            'project' => $project,
            'properties' => $properties,
        ]);
    }
}
