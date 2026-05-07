<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('status.name')
                    ->label('Status')
                    ->collapsible(),
                Group::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->collapsible(),
            ])
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending'     => 'gray',
                        'Confirmed'   => 'info',
                        'Processing'  => 'warning',
                        'Shipped'     => 'primary',
                        'Delivered'   => 'success',
                        'Cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('total_quantity')
                    ->label('Qty')
                    ->getStateUsing(fn($record): int => $record->items->sum('quantity'))
                    ->alignCenter(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('NPR')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->getStateUsing(function ($record): string {
                        return optional($record->payments()->latest()->first())->method ?? '—';
                    })
                    ->alignCenter(),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid'    => 'success',
                        'Pending' => 'warning',
                        'Failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->getStateUsing(function ($record): string {
                        return optional($record->payments()->latest()->first())->status ?? 'Pending';
                    }),

                TextColumn::make('shipping_address')
                    ->label('Shipping Address')
                    ->getStateUsing(function ($record): string {
                        if (!$record->address) {
                            return '—';
                        }
                        return implode(', ', array_filter([
                            $record->address->address_line1,
                            optional($record->address->district)->name,
                            optional($record->address->province)->name,
                        ]));
                    })
                    ->wrap()
                    ->searchable(false),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->placeholder('All Statuses'),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Action::make('viewItems')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn($record) => "Order #{$record->order_number}")
                    ->modalContent(fn($record) => view('filament/order-items-modal', [
                        'order' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])

            ->recordUrl(
                fn($record): string => route(
                    'filament.admin.resources.orders.edit',
                    $record
                )
            )

            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
