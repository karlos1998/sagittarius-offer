<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportAssortmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'guns' => ['required_without:ammunitions', 'array', 'max:500'],
            'guns.*' => ['required', 'array:id,name,gun_type,caliber,description'],
            'guns.*.id' => ['sometimes', 'integer', 'distinct', 'exists:guns,id'],
            'guns.*.name' => ['required', 'string', 'max:255'],
            'guns.*.gun_type' => ['required', 'string', 'max:255'],
            'guns.*.caliber' => ['required', 'string', 'max:255'],
            'guns.*.description' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'ammunitions' => ['required_without:guns', 'array', 'max:500'],
            'ammunitions.*' => ['required', 'array:id,name,caliber,club_price,standard_price,cart_quantity_step'],
            'ammunitions.*.id' => ['sometimes', 'integer', 'distinct', 'exists:ammunitions,id'],
            'ammunitions.*.name' => ['required', 'string', 'max:255'],
            'ammunitions.*.caliber' => ['required', 'string', 'max:255'],
            'ammunitions.*.club_price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'ammunitions.*.standard_price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99', 'decimal:0,2'],
            'ammunitions.*.cart_quantity_step' => ['sometimes', 'integer', 'min:1', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Pole :attribute jest wymagane.',
            'required_without' => 'Podaj listę broni lub amunicji.',
            'exists' => 'Pozycja :attribute nie istnieje.',
            'distinct' => 'To samo ID występuje więcej niż raz.',
            'array' => 'Pole :attribute musi być listą lub obiektem z dozwolonymi polami.',
            'max' => 'Pole :attribute przekracza dozwolony limit :max.',
            'min' => 'Pole :attribute musi mieć wartość co najmniej :min.',
            'decimal' => 'Cena może mieć najwyżej dwa miejsca po przecinku.',
        ];
    }
}
