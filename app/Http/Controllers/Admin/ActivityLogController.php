<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ActivityLog, User};
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }
        if ($request->filled('action')) {
            $query->action($request->action);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->betweenDates($request->from, $request->to);
        }

        $logs = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.show', compact('activityLog'));
    }

    /** Full audit trail for a specific subject (e.g. points dispute resolution) */
    public function trailFor(Request $request)
    {
        $request->validate([
            'subject_type' => 'required|string',
            'subject_id'   => 'required|integer',
        ]);

        $logs = ActivityLog::where('subject_type', $request->subject_type)
            ->where('subject_id', $request->subject_id)
            ->with('user')
            ->latest()
            ->get();

        return view('admin.activity-logs.trail', compact('logs'));
    }

    /** Export Log function (CSV) */
    public function export(Request $request)
    {
        $rows = $this->activityLog->exportLogs(
            $request->user_id,
            $request->action,
            $request->from,
            $request->to
        );

        $filename = 'activity-logs-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'User', 'Action', 'Subject', 'Metadata', 'IP Address', 'Date']);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
