<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CmsActivityController extends Controller
{
    public function index(Request $request): View
    {
        $dateRange = $this->validateDateRange($request);
        $activityLogs = $this->activityQuery($dateRange)
            ->with('user')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', [
            'activityLogs' => $activityLogs,
            ...$dateRange,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $dateRange = $this->validateDateRange($request);
        $activityQuery = $this->activityQuery($dateRange);
        $filename = 'cms-activity-'.$dateRange['start_date'].'-to-'.$dateRange['end_date'].'.txt';

        return response()->streamDownload(function () use ($activityQuery): void {
            foreach ($activityQuery->orderBy('created_at')->orderBy('id')->cursor() as $activity) {
                $fields = [
                    $activity->created_at->timezone(config('cms.timezone'))->format('Y-m-d H:i:s'),
                    $activity->actor_name,
                    $activity->action,
                    $activity->subject_type,
                    $activity->subject_name,
                    $activity->details ?? '-',
                ];
                $line = implode(' | ', array_map(
                    fn (string $field): string => Str::replace(["\r", "\n"], ' ', $field),
                    $fields,
                ));

                echo $line.PHP_EOL;
            }
        }, $filename, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * @return array{start_date: string|null, end_date: string|null}
     */
    private function validateDateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        return [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ];
    }

    /**
     * @param  array{start_date: string|null, end_date: string|null}  $dateRange
     */
    private function activityQuery(array $dateRange): Builder
    {
        return CmsActivityLog::query()
            ->when(
                $dateRange['start_date'],
                fn (Builder $query, string $startDate): Builder => $query->where(
                    'created_at',
                    '>=',
                    Carbon::parse($startDate, config('cms.timezone'))->startOfDay()->utc(),
                ),
            )
            ->when(
                $dateRange['end_date'],
                fn (Builder $query, string $endDate): Builder => $query->where(
                    'created_at',
                    '<',
                    Carbon::parse($endDate, config('cms.timezone'))->addDay()->startOfDay()->utc(),
                ),
            );
    }
}
