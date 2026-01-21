<?php

namespace App\Http\Requests\Cart;

use App\Enums\CartAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCartRequest extends FormRequest
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
            'gun_id' => 'required|integer|exists:guns,id',
            'action' => ['required', new Enum(CartAction::class)],
            'ammo_id' => 'nullable|integer|exists:ammunitions,id',
        ];
    }

    /**
     * Get the validated action as enum instance
     */
    public function getAction(): CartAction
    {
        return CartAction::from($this->validated()['action']);
    }

    /**
     * Get the validated gun ID
     */
    public function getGunId(): int
    {
        return $this->validated()['gun_id'];
    }

    /**
     * Get the validated ammo ID (nullable)
     */
    public function getAmmoId(): ?int
    {
        return $this->validated()['ammo_id'];
    }
}
