<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Lead::query()->with('property')->latest()->limit(5))
            ->columns([
                TextColumn::make('property.title')->limit(30)->label('Properti'),
                TextColumn::make('name')->searchable()->label('Nama'),
                TextColumn::make('phone')->label('Telepon'),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Masuk'),
            ])
            ->paginated(false);
    }
}
