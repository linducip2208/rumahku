<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyOrder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $listings = Property::query()->whereNotNull('published_at')->count();
        $views = (int) Property::query()->sum('views');
        $leadsThisMonth = Lead::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $revenueThisMonth = (int) PropertyOrder::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $listingTrend = $this->dailyCounts(Property::query()->whereNotNull('published_at'), 'published_at');
        $viewsTrend = $this->dailySums(Property::query(), 'created_at', 'views');
        $leadsTrend = $this->dailyCounts(Lead::query(), 'created_at');
        $revenueTrend = $this->dailySums(PropertyOrder::query()->where('status', 'paid'), 'paid_at', 'amount');

        return [
            Stat::make('Total Listing Published', number_format($listings))
                ->description('Properti tayang')
                ->descriptionIcon('heroicon-m-home-modern')
                ->icon('heroicon-o-home-modern')
                ->color('primary')
                ->chart($listingTrend),
            Stat::make('Total Views', number_format($views))
                ->description('Akumulasi views properti')
                ->descriptionIcon('heroicon-m-eye')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->chart($viewsTrend),
            Stat::make('Leads Bulan Ini', number_format($leadsThisMonth))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->icon('heroicon-o-users')
                ->color('success')
                ->chart($leadsTrend),
            Stat::make('Revenue Bulan Ini', 'Rp '.number_format($revenueThisMonth, 0, ',', '.'))
                ->description('Order paid · '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->chart($revenueTrend),
        ];
    }

    /**
     * @param  Builder<Model>  $query
     * @return array<float>
     */
    private function dailyCounts(object $query, string $column): array
    {
        $out = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $out[] = (float) (clone $query)->whereDate($column, $date)->count();
        }

        return $out;
    }

    /**
     * @param  Builder<Model>  $query
     * @return array<float>
     */
    private function dailySums(object $query, string $column, string $sumColumn): array
    {
        $out = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $out[] = (float) (clone $query)->whereDate($column, $date)->sum($sumColumn);
        }

        return $out;
    }
}
