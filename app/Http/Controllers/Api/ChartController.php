<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chart\GetSummaryTodoRequest;
use App\Models\Todo;

class ChartController extends Controller
{
    public function __invoke(GetSummaryTodoRequest $request)
    {
        return match ($request->input('type')) {
            'status' => $this->statusSummary(),
            'priority' => $this->prioritySummary(),
            'assignee' => $this->assigneeSummary(),
        };
    }

    private function statusSummary()
    {
        $data = Todo::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return response()->json([
            'status_summary' => $data,
        ]);
    }

    private function prioritySummary()
    {
        $data = Todo::query()
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        return response()->json([
            'priority_summary' => $data,
        ]);
    }

    private function assigneeSummary()
    {
        $rows = Todo::query()
            ->select('assignee')
            ->selectRaw('COUNT(*) as total_todos')
            ->selectRaw(
                "COUNT(*) FILTER (WHERE status = 'pending') as total_pending_todos"
            )
            ->selectRaw(
                "COALESCE(SUM(time_tracked) FILTER (WHERE status = 'completed'), 0)
                as total_timetracked_completed_todos"
            )
            ->whereNotNull('assignee')
            ->groupBy('assignee')
            ->orderBy('assignee')
            ->get();

        $data = $rows->mapWithKeys(fn($row) => [
            $row->assignee => [
                'total_todos' => (int) $row->total_todos,
                'total_pending_todos' => (int) $row->total_pending_todos,
                'total_timetracked_completed_todos' =>
                (int) $row->total_timetracked_completed_todos,
            ],
        ]);

        return response()->json([
            'assignee_summary' => $data,
        ]);
    }
}
