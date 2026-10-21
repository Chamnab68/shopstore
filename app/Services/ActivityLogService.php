<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Module #2 — Activity Log (Mandatory).
 * Central service for recording all loyalty-platform actions with full context.
 */
class ActivityLogService
{
    /**
     * Log an action with optional subject model and metadata.
     *
     * @param string $action  e.g. "points.earned", "tier.upgrade", "badge.awarded"
     * @param Model|null $subject The model this action relates to
     * @param array $data Extra data: old_values, new_values, metadata
     */
    public function log(string $action, ?Model $subject = null, array $data = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'old_values'   => $data['old_values'] ?? null,
            'new_values'   => $data['new_values'] ?? null,
            'metadata'     => $data['metadata'] ?? null,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }

    /** Convenience: log a points transaction with before/after balance */
    public function logPointsChange(int $userId, int $balanceBefore, int $balanceAfter, array $metadata = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id'      => $userId,
            'action'       => 'points.balance_changed',
            'old_values'   => ['balance' => $balanceBefore],
            'new_values'   => ['balance' => $balanceAfter],
            'metadata'     => $metadata,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }

    /** Get full audit trail for a specific subject (e.g. for points dispute resolution) */
    public function getTrailFor(Model $subject): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->latest()
            ->get();
    }

    /** Export logs as array for CSV (Module #2: "Export Log" function) */
    public function exportLogs(?string $userId = null, ?string $action = null, ?string $from = null, ?string $to = null): array
    {
        $query = ActivityLog::with('user')->latest();

        if ($userId) $query->forUser($userId);
        if ($action)  $query->action($action);
        if ($from && $to) $query->betweenDates($from, $to);

        return $query->get()->map(fn ($log) => [
            'id'         => $log->id,
            'user'       => $log->user->name ?? 'System',
            'action'     => $log->action,
            'subject'    => $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '-',
            'metadata'   => json_encode($log->metadata),
            'ip_address' => $log->ip_address,
            'created_at' => $log->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}
