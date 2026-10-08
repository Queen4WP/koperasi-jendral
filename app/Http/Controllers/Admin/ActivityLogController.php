<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['actor', 'subject'])->latest('id');

        if ($request->filled('action')) {
            $query->where('activity', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('actor', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        
        $distinctActions = ActivityLog::query()
            ->select('activity')
            ->distinct()
            ->orderBy('activity')
            ->pluck('activity');

        $action = $request->action;
        $search = $request->search;

        return view('admin.activity-logs.index', compact('logs', 'distinctActions', 'action', 'search'));
    }
}
