<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Project::query()->orderByDesc('id');

        if (! empty($validated['city'])) {
            $query->where('city', 'like', '%'.$validated['city'].'%');
        }

        if (! empty($validated['q'])) {
            $query->where('name', 'like', '%'.$validated['q'].'%');
        }

        return ProjectResource::collection($query->paginate(15)->withQueryString());
    }

    public function show(string $slug)
    {
        $project = Project::query()->where('slug', $slug)->firstOrFail();

        return new ProjectResource($project);
    }
}
