<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public const STATUSES = ['baru', 'dihubungi', 'survei', 'negosiasi', 'closing', 'gagal'];

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $leads = Lead::query()
            ->where('agent_user_id', $request->user()->id)
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->with('property')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('agent.leads.index', [
            'leads' => $leads,
            'statuses' => self::STATUSES,
            'activeStatus' => in_array($status, self::STATUSES, true) ? $status : null,
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->agent_user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:baru,dihubungi,survei,negosiasi,closing,gagal'],
            'follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead->update($validated);

        return back()->with('success', 'Status lead berhasil diperbarui.');
    }
}
