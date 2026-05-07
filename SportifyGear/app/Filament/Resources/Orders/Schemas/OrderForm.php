<?php

namespace App\Filament\Resources\Orders\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

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

                    // TextInput::make('coupon_display')
                    //     ->label('Coupon')
                    //     ->formatStateUsing(function ($record) {

                    //         return $record?->coupon?->code
                    //             ?? 'No Coupon Used';
                    //     })
                    //     ->disabled(),
                ])
                ->columnSpanFull(),

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


            Section::make('Payment Information')
                ->description('Latest payment details.')
                ->icon('heroicon-o-credit-card')
                ->collapsible()
                ->compact()
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                ])
                ->schema([

                    TextEntry::make('payment_method')
                        ->label('Payment Method')
                        ->state(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return $payment?->method ?: 'N/A';
                        })
                        ->badge(),

                    TextEntry::make('payment_status')
                        ->label('Payment Status')
                        ->state(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return $payment?->status ?: 'Pending';
                        })
                        ->badge()
                        ->color(function ($state) {

                            return match (strtolower($state)) {
                                'paid',
                                'completed',
                                'success' => 'success',

                                'pending' => 'warning',

                                'failed',
                                'cancelled' => 'danger',

                                default => 'gray',
                            };
                        }),

                    TextEntry::make('transaction_id')
                        ->label('Transaction ID')
                        ->state(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            return $payment?->transaction_id ?: 'N/A';
                        })
                        ->copyable(),

                    TextEntry::make('paid_at')
                        ->label('Paid At')
                        ->state(function ($record) {

                            $payment = $record->payments()->latest()->first();

                            if (!$payment || !$payment->paid_at) {
                                return 'N/A';
                            }

                            return Carbon::parse($payment->paid_at)
                                ->format('M j, Y h:i A');
                        }),
                ])
                ->columnSpanFull(),
        ]);
    }
}
