<?php

namespace App\Exports;

use App\Models\Todo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use Maatwebsite\Excel\Events\AfterSheet;

class TodoExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents,
    WithPreCalculateFormulas
{
    use Exportable;

    public function __construct(
        private ?string $title,
        private array $assignees,
        private array $statuses,
        private array $priorities,
        private ?Carbon $start,
        private ?Carbon $end,
        private ?int $min,
        private ?int $max,
    ) {}

    public function query(): Builder
    {
        return Todo::query()
            ->select([
                'title',
                'assignee',
                'due_date',
                'time_tracked',
                'status',
                'priority',
            ])
            ->when($this->title, fn (Builder $q) =>
                $q->where('title', 'ilike', "%{$this->title}%")
            )
            ->when($this->assignees !== [], fn (Builder $q) =>
                $q->whereIn('assignee', $this->assignees)
            )
            ->when($this->statuses !== [], fn (Builder $q) =>
                $q->whereIn('status', $this->statuses)
            )
            ->when($this->priorities !== [], fn (Builder $q) =>
                $q->whereIn('priority', $this->priorities)
            )
            ->when(
                $this->start && $this->end,
                fn (Builder $q) =>
                    $q->whereBetween('due_date', [$this->start, $this->end])
            )
            ->when(!is_null($this->min), fn (Builder $q) =>
                $q->where('time_tracked', '>=', $this->min)
            )
            ->when(!is_null($this->max), fn (Builder $q) =>
                $q->where('time_tracked', '<=', $this->max)
            )

            ->orderBy('due_date')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority',
        ];
    }

    public function map($todo): array
    {
        return [
            $todo->title,
            $todo->assignee,
            $todo->due_date,
            $todo->time_tracked,
            $todo->status->label(),
            $todo->priority->name,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastDataRow = $sheet->getHighestRow();
                $summaryStartRow = $lastDataRow + 2;

                $sheet->setCellValue("A{$summaryStartRow}", 'Total Todo List');
                $sheet->setCellValue(
                    "B{$summaryStartRow}",
                    "=COUNTA(A2:A{$lastDataRow})"
                );

                $sheet->setCellValue("A" . ($summaryStartRow + 1), 'Total Time Tracked');
                $sheet->setCellValue(
                    "B" . ($summaryStartRow + 1),
                    "=SUM(D2:D{$lastDataRow})"
                );

                $sheet->getStyle(
                    "A{$summaryStartRow}:B" . ($summaryStartRow + 1)
                )->getFont()->setBold(true);
            },
        ];
    }
}
