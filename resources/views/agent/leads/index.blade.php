@extends('layouts.metronic')

@section('title', 'Leads Saya — Rumah123')

@section('content')
<section class="max-w-7xl mx-auto px-4 lg:px-6 py-10">
    <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">Leads Saya</h1>
    <p class="text-sm text-muted-foreground mt-1">Kelola pipeline calon pembeli properti Anda.</p>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mt-6">
        <a href="{{ route('dashboard.leads.index') }}"
            class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold {{ $activeStatus === null ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-border' }}">
            Semua
        </a>
        @foreach ($statuses as $status)
            <a href="{{ route('dashboard.leads.index', ['status' => $status]) }}"
                class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold capitalize {{ $activeStatus === $status ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-border' }}">
                {{ $status }}
            </a>
        @endforeach
    </div>

    @if ($leads->isEmpty())
        <div class="rounded-2xl border border-dashed border-border p-10 text-center text-muted-foreground mt-6">
            Belum ada leads{{ $activeStatus ? ' dengan status "'.$activeStatus.'"' : '' }}.
        </div>
    @else
        <div class="kt-card mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-muted-foreground border-b border-border">
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">Properti</th>
                        <th class="px-4 py-3 font-semibold">Telepon</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Follow Up</th>
                        <th class="px-4 py-3 font-semibold">Ubah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($leads as $lead)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $lead->name }}</td>
                            <td class="px-4 py-3">{{ $lead->property?->title ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $lead->phone }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize bg-muted">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ $lead->follow_up_at ? $lead->follow_up_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('dashboard.leads.update', $lead) }}" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="rounded-lg border border-border text-xs px-2 py-1.5" required>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($lead->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="datetime-local" name="follow_up_at"
                                        value="{{ $lead->follow_up_at ? $lead->follow_up_at->format('Y-m-d\TH:i') : '' }}"
                                        class="rounded-lg border border-border text-xs px-2 py-1.5">
                                    <input type="text" name="notes" value="{{ $lead->notes }}"
                                        placeholder="Catatan" maxlength="2000"
                                        class="rounded-lg border border-border text-xs px-2 py-1.5 w-32">
                                    <button type="submit" class="kt-btn kt-btn-primary kt-btn-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $leads->links() }}</div>
    @endif
</section>
@endsection
