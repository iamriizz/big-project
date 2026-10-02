<?php

namespace App\Filament\Resources\SalesReportResource\Pages;

use App\Filament\Resources\SalesReportResource;
use App\Filament\Widgets\SalesReportSummary;
use Filament\Resources\Pages\ListRecords;

class ListSalesReports extends ListRecords
{
    protected static string $resource = SalesReportResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SalesReportSummary::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            "tableFilters" => $this->tableFilters ?? [],
        ];
    }
}
