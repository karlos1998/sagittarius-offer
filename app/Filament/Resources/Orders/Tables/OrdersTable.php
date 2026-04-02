<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Numer')
                    ->searchable(),
                TextColumn::make('customer_full_name')
                    ->label('Klient')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status zamówienia')
                    ->badge()
                    ->state(fn (Order $record): string => $record->statusLabel())
                    ->color(fn (Order $record): string => $record->status->value === 'confirmed' ? 'success' : 'warning'),
                TextColumn::make('payment_status')
                    ->label('Status płatności')
                    ->badge()
                    ->state(fn (Order $record): string => $record->paymentStatusLabel())
                    ->color(fn (Order $record): string => $record->payment_status->value === 'paid' ? 'success' : 'danger'),
                TextColumn::make('total_amount')
                    ->label('Kwota')
                    ->money('PLN')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                Action::make('mark_as_paid')
                    ->label('Oznacz jako opłacone')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->canBeMarkedAsPaid())
                    ->action(function (Order $record): void {
                        $record->markAsPaid();
                    }),
            ])
            ->toolbarActions([]);
    }
}
