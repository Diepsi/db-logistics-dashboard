<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentIssue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = ShipmentIssue::with(['shipment', 'resolvedBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('shipment', fn ($sq) => $sq->where('waybill_no', 'like', "%{$search}%"));
            })
            ->latest('reported_at')
            ->paginate(20)
            ->withQueryString();

        return view('issues.index', compact('issues'));
    }

    public function resolve(ShipmentIssue $issue)
    {
        $issue->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Issue berhasil diselesaikan.');
    }

    public function reopen(ShipmentIssue $issue)
    {
        $issue->update([
            'status' => 'open',
            'resolved_by' => null,
            'resolved_at' => null,
        ]);

        return back()->with('success', 'Issue berhasil dibuka kembali.');
    }
}
