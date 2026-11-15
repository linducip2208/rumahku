<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        return view('agent.dashboard', [
            'totalListings' => Property::query()->where('user_id', $userId)->count(),
            'totalViews' => (int) Property::query()->where('user_id', $userId)->sum('views'),
            'totalLeads' => Lead::query()->where('agent_user_id', $userId)->count(),
            'pendingOrders' => PropertyOrder::query()->where('user_id', $userId)->where('status', 'pending')->count(),
            'latestProperties' => Property::query()->where('user_id', $userId)->with('images')->latest()->take(5)->get(),
            'latestLeads' => Lead::query()->where('agent_user_id', $userId)->with('property')->latest()->take(5)->get(),
        ]);
    }
}
