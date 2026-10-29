<?php

namespace App\Services;

use App\Models\CmsActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use LogicException;

class CmsActivityLogger
{
    /**
     * @param  array<int, int>  $subjectIds
     * @param  array<int, string>  $actions
     * @return Collection<int, string>
     */
    public function latestActorsFor(string $subjectType, array $subjectIds, array $actions = ['uploaded']): Collection
    {
        if ($subjectIds === []) {
            return collect();
        }

        return CmsActivityLog::query()
            ->where('subject_type', $subjectType)
            ->whereIn('subject_id', $subjectIds)
            ->whereIn('action', $actions)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['subject_id', 'actor_name'])
            ->unique('subject_id')
            ->mapWithKeys(fn (CmsActivityLog $activity): array => [
                $activity->subject_id => $activity->actor_name,
            ]);
    }

    public function record(
        Request $request,
        string $action,
        string $subjectType,
        ?int $subjectId,
        string $subjectName,
        ?string $details = null,
    ): void {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new LogicException('CMS activity can only be recorded for an authenticated CMS user.');
        }

        CmsActivityLog::create([
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_name' => $subjectName,
            'details' => $details,
        ]);
    }
}
