<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $usersThisMonth = User::whereMonth('created_at', now()->month)->count();

        $activeSubscriptions = DB::table('subscriptions')
            ->where('stripe_status', 'active')
            ->count();

        $mrr = DB::table('subscriptions')
            ->join('subscription_items', 'subscriptions.id', '=', 'subscription_items.subscription_id')
            ->where('subscriptions.stripe_status', 'active')
            ->sum(DB::raw('subscription_items.quantity * 0'));

        return [
            Stat::make('Total Users', $totalUsers)
                ->description($usersThisMonth.' new this month')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success')
                ->chart([7, 10, 15, 12, 18, 22, $totalUsers]),
            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('info'),
            Stat::make('Monthly Recurring Revenue', '$'.number_format($mrr, 2))
                ->description('Estimated MRR')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
