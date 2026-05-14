<?php

namespace App\Filament\Resources\Orders\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Order Overview
            |--------------------------------------------------------------------------
            */

            Section::make('Order Overview')
                ->description('Manage order status and view customer information.')
                ->icon('heroicon-o-shopping-bag')
                ->collapsible()
                ->compact()
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                    'xl' => 3,
                ])
                ->schema([

                    TextInput::make('order_number')
                        ->label('Order Number')
                        ->disabled()
                        ->prefixIcon('heroicon-m-hashtag'),

                    Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('status_id')
                        ->label('Order Status')
                        ->relationship('status', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),

                    TextInput::make('sub_total')
                        ->label('Subtotal')
                        ->prefix('Rs')
                        ->disabled(),

                    TextInput::make('total')
                        ->label('Grand Total')
                        ->prefix('Rs')
                        ->disabled(),
                ])
                ->columnSpanFull(),

            /*
            |--------------------------------------------------------------------------
            | Order Items
            |--------------------------------------------------------------------------
            */

            Section::make('Order Items')
                ->description('Products included in this order.')
                ->icon('heroicon-o-cube')
                ->collapsible()
                ->collapsed(false)
                ->compact()
                ->columnSpanFull()
                ->schema([

                    Repeater::make('items')
                        ->relationship()
                        ->disabled()
                        ->dehydrated(false)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->cloneable(false)
                        ->columnSpanFull()

                        ->itemLabel(function (array $state): ?string {

                            return $state['product_name']
                                ?? $state['name']
                                ?? 'Order Item';
                        })

                        ->schema([

                            Grid::make([
                                'default' => 1,
                                'sm' => 2,
                                'lg' => 12,
                            ])
                                ->schema([

                                    TextInput::make('product_display')
                                        ->label('Product')
                                        ->formatStateUsing(function ($record) {

                                            return $record?->product?->name
                                                ?? 'Unknown Product';
                                        })
                                        ->disabled()
                                        ->columnSpan([
                                            'default' => 1,
                                            'sm' => 2,
                                            'lg' => 5,
                                        ]),

                                    TextInput::make('variant_display')
                                        ->label('Variant')
                                        ->formatStateUsing(function ($record) {

                                            return $record?->variant?->name
                                                ?? 'Default Variant';
                                        })
                                        ->disabled()
                                        ->columnSpan([
                                            'default' => 1,
                                            'sm' => 1,
                                            'lg' => 3,
                                        ]),

                                    TextInput::make('quantity')
                                        ->label('Qty')
                                        ->numeric()
                                        ->disabled()
                                        ->columnSpan([
                                            'default' => 1,
                                            'sm' => 1,
                                            'lg' => 2,
                                        ]),

                                    TextInput::make('price')
                                        ->label('Unit Price')
                                        ->prefix('Rs')
                                        ->disabled()
                                        ->columnSpan([
                                            'default' => 1,
                                            'sm' => 1,
                                            'lg' => 2,
                                        ]),
                                ]),
                        ]),
                ]),

            /*
            |--------------------------------------------------------------------------
            | Payment Information
            |--------------------------------------------------------------------------
            */

            Section::make('Payment Information')
                ->description('Manage payment details for this order.')
                ->icon('heroicon-o-credit-card')
                ->collapsible()
                ->compact()
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | Payment Method
                    |--------------------------------------------------------------------------
                    */

                    Placeholder::make('payment_method')
                        ->label('Payment Method')
                        ->content(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return strtoupper($payment?->method ?? 'N/A');
                        }),

                    /*
                    |--------------------------------------------------------------------------
                    | Payment Status
                    |--------------------------------------------------------------------------
                    */

                    Select::make('payment_status')
                        ->label('Payment Status')

                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'refunded' => 'Refunded',
                        ])

                        // Show database value
                        ->formatStateUsing(function ($record) {

                            return $record->payments()
                                ->latest()
                                ->first()?->status ?? 'pending';
                        })

                        // Editable only for COD
                        ->disabled(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return !$payment || $payment->method !== 'cod';
                        })

                        ->live()

                        ->native(false)

                        ->dehydrated(false)

                        ->afterStateUpdated(function ($state, $record, callable $set) {

                            $payment = $record->payments()->latest()->first();

                            if (!$payment) {
                                return;
                            }

                            $paidAt = $state === 'paid'
                                ? ($payment->paid_at ?? now())
                                : null;

                            $payment->update([
                                'status' => $state,
                                'paid_at' => $paidAt,
                            ]);

                            $set('paid_at', $paidAt);
                        }),

                    /*
                    |--------------------------------------------------------------------------
                    | Transaction ID
                    |--------------------------------------------------------------------------
                    */

                    TextInput::make('transaction_id')
                        ->label('Transaction ID')
                        ->formatStateUsing(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return $payment?->transaction_id ?: 'N/A';
                        })
                        ->disabled(),

                    /*
                    |--------------------------------------------------------------------------
                    | Paid At
                    |--------------------------------------------------------------------------
                    */

                    DateTimePicker::make('paid_at')
                        ->label('Paid At')

                        ->seconds(false)

                        ->native(false)

                        // Show database value
                        ->formatStateUsing(function ($record) {

                            return $record->payments()
                                ->latest()
                                ->first()?->paid_at;
                        })

                        // Editable only for COD
                        ->disabled(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return !$payment || $payment->method !== 'cod';
                        })

                        ->dehydrated(false)

                        ->live()

                        ->afterStateUpdated(function ($state, $record) {

                            $payment = $record->payments()->latest()->first();

                            if (!$payment) {
                                return;
                            }

                            $payment->update([
                                'paid_at' => $state,
                            ]);
                        }),
                ])
                ->columnSpanFull(),
        ]);
    }
}
