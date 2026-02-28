<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->label('Numer zamówienia')
                    ->disabled(),
                TextInput::make('first_name')
                    ->label('Imię')
                    ->disabled(),
                TextInput::make('last_name')
                    ->label('Nazwisko')
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->disabled(),
            ]);
    }
}
