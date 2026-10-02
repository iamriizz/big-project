<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Filament\Widgets\MonthlySalesTrendChart;
use App\Filament\Widgets\RecentBranchTransactions;
use App\Filament\Widgets\StatsOverview;
use Filament\Widgets\AccountWidget;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getWidgets(): array
    {
        if (auth()->user()?->isOwner()) {
            return [
                AccountWidget::class,
                MonthlySalesTrendChart::class,
                StatsOverview::class,
            ];
        }

        if (auth()->user()?->isAdmin()) {
            return [
                AccountWidget::class,
                StatsOverview::class,
                RecentBranchTransactions::class,
            ];
        }

        return parent::getWidgets();
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Select::make('branch_id')
                ->label('Cabang')
                ->placeholder('Semua cabang')
                ->options(fn (): array => Branch::query()->official()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->visible(fn (): bool => auth()->user()?->isOwner() ?? false),
        ]);
    }
}
