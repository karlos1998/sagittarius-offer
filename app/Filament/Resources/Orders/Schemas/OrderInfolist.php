<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number')
                    ->label('Numer zamówienia'),
                TextEntry::make('customer_full_name')
                    ->label('Klient'),
                TextEntry::make('email')
                    ->label('E-mail'),
                TextEntry::make('full_address')
                    ->label('Adres')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('Status zamówienia')
                    ->badge()
                    ->state(fn ($record): string => $record->statusLabel())
                    ->color(fn ($record): string => $record->status->value === 'confirmed' ? 'success' : 'warning'),
                TextEntry::make('payment_method')
                    ->label('Forma płatności')
                    ->state(fn ($record): string => $record->paymentMethodLabel()),
                TextEntry::make('payment_status')
                    ->label('Status płatności')
                    ->badge()
                    ->state(fn ($record): string => $record->paymentStatusLabel())
                    ->color(fn ($record): string => $record->payment_status->value === 'paid' ? 'success' : 'danger'),
                TextEntry::make('total_shots')
                    ->label('Łącznie strzałów'),
                TextEntry::make('total_amount')
                    ->label('Łączna kwota')
                    ->money('PLN'),
                RepeatableEntry::make('items')
                    ->label('Pozycje zamówienia')
                    ->columnSpanFull()
                    ->table([
                        TableColumn::make('Broń'),
                        TableColumn::make('Amunicja'),
                        TableColumn::make('Strzały'),
                        TableColumn::make('Cena/strzał'),
                        TableColumn::make('Wartość'),
                    ])
                    ->schema([
                        TextEntry::make('gun_name'),
                        TextEntry::make('ammunition_name'),
                        TextEntry::make('quantity'),
                        TextEntry::make('price_per_shot')
                            ->money('PLN'),
                        TextEntry::make('line_total')
                            ->money('PLN'),
                    ]),
                TextEntry::make('created_at')
                    ->label('Utworzono')
                    ->dateTime(),
                TextEntry::make('verified_at')
                    ->label('Potwierdzono')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->label('Opłacono')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
