<x-app-layout>
    <x-slot name="header">
        Manajemen Issue
    </x-slot>

    <div class="space-y-6">
        <!-- Filter -->
        <div class="card p-5">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="field-label">Cari No Resi</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No resi..." class="field-input">
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <select name="status" class="field-input">
                        <option value="">Semua</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Filter</button>
            </form>
        </div>

        <!-- Issues Table -->
        <div class="card overflow-hidden">
            @if($issues->isEmpty())
                <div class="p-12 text-center">
                    <div class="icon-chip bg-emerald-50 text-emerald-500 mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Tidak ada issue ditemukan</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-[11px] text-gray-400 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3 font-bold">No Resi</th>
                                <th class="px-5 py-3 font-bold">Tipe Issue</th>
                                <th class="px-5 py-3 font-bold">Deskripsi</th>
                                <th class="px-5 py-3 font-bold">Dilaporkan</th>
                                <th class="px-5 py-3 font-bold">Status</th>
                                <th class="px-5 py-3 font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($issues as $issue)
                                <tr class="transition-colors hover:bg-dbl-green-light/20">
                                    <td class="px-5 py-3.5">
                                        @if($issue->shipment)
                                            <a href="{{ route('shipments.show', $issue->shipment->id) }}" class="font-mono text-xs font-semibold text-dbl-green-dark hover:underline">
                                                {{ $issue->shipment->waybill_no }}
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-sm font-semibold text-gray-800">{{ $issue->issue_type }}</td>
                                    <td class="px-5 py-3.5 text-xs text-gray-500 max-w-xs truncate">{{ $issue->description }}</td>
                                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $issue->reported_at?->format('d M Y H:i') }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="badge {{ $issue->status === 'open' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            <span class="dot {{ $issue->status === 'open' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                            {{ ucfirst($issue->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($issue->status === 'open')
                                            <form method="POST" action="{{ route('issues.resolve', $issue) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">Selesaikan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('issues.reopen', $issue) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-amber-600 hover:text-amber-800 transition-colors">Buka Kembali</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $issues->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
