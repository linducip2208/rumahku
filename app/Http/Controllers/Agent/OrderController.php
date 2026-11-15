<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\PropertyOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = PropertyOrder::query()
            ->where('user_id', $request->user()->id)
            ->with('property')
            ->latest()
            ->paginate(12);

        return view('agent.orders', compact('orders'));
    }
}
