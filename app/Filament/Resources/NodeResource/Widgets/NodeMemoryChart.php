<?php

namespace App\Filament\Resources\NodeResource\Widgets;

use App\Models\Node;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class NodeMemoryChart extends ChartWidget
{
    protected static ?string $heading = 'Memory';
    protected static ?string $pollingInterval = '5s';
    protected static ?string $maxHeight = '300px';

    public ?Model $record = null;

    protected static ?array $options = [
        'scales' => [
            'y' => [
                'min' => 0,
            ],
        ],
        'tooltips' => [
            'enabled' => true,
        ],
        'plugins' => [
            'datalabels' => [],
        ],
    ];

    protected function getData(): array
    {
        /** @var Node $node */
        $node = $this->record;

        $memUsed = collect(cache()->get("nodes.$node->id.memory_used"))->slice(-10)
            ->map(fn ($value, $key) => [
                'memory' => $value / 1024 / 1024 / 1024,
                'timestamp' => Carbon::createFromTimestamp($key)->format('H:i:s'),
            ]
            )
            ->all();

        return [
            'datasets' => [
                [
                    'data' => array_column($memUsed, 'memory'),
                    'backgroundColor' => [
                        'rgba(96, 165, 250, 0.2)',
                    ],
                    'tension' => '0.3',
                    'fill' => true,
                    'label' => 'Memory Usage',
                    'datalabels' => true,
                ],
            ],
            'labels' => array_column($memUsed, 'timestamp'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

}
