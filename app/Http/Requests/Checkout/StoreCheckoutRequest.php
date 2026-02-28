<?php

namespace App\Http\Requests\Checkout;

use App\Enums\OrderPaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'street' => ['required', 'string', 'max:150'],
            'house_number' => ['required', 'string', 'max:20'],
            'apartment_number' => ['nullable', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^[0-9]{2}-[0-9]{3}$/'],
            'city' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'payment_method' => ['required', Rule::in([OrderPaymentMethod::PayOnSite->value])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'street.required' => 'Ulica jest wymagana.',
            'house_number.required' => 'Numer domu jest wymagany.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'postal_code.regex' => 'Kod pocztowy musi być w formacie 00-000.',
            'city.required' => 'Miasto jest wymagane.',
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.email' => 'Podaj poprawny adres e-mail.',
            'payment_method.required' => 'Wybierz formę płatności.',
            'payment_method.in' => 'Wybrana forma płatności jest nieprawidłowa.',
        ];
    }

    public function getFirstName(): string
    {
        return $this->validated()['first_name'];
    }

    public function getLastName(): string
    {
        return $this->validated()['last_name'];
    }

    public function getStreet(): string
    {
        return $this->validated()['street'];
    }

    public function getHouseNumber(): string
    {
        return $this->validated()['house_number'];
    }

    public function getApartmentNumber(): ?string
    {
        return $this->validated()['apartment_number'] ?: null;
    }

    public function getPostalCode(): string
    {
        return $this->validated()['postal_code'];
    }

    public function getCity(): string
    {
        return $this->validated()['city'];
    }

    public function getEmail(): string
    {
        return $this->validated()['email'];
    }

    public function getPaymentMethod(): string
    {
        return $this->validated()['payment_method'];
    }
}
