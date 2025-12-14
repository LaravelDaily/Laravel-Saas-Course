<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserGrowthChart extends ChartWidget
{
    protected ?string $heading = 'User Growth';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getUsersPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data['counts'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getUsersPerMonth(): array
    {
        $months = collect();
        $counts = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $counts->push($count);
        }

        return [
            'months' => $months->toArray(),
            'counts' => $counts->toArray(),
        ];
    }
}
