<?php

namespace App\Filament\Widgets;

use App\Models\PropertyOrder;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Revenue per Hari';

    protected ?string $description = 'Order paid (IDR) 14 hari terakhir.';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            $values[] = (int) PropertyOrder::query()
                ->where('status', 'paid')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
