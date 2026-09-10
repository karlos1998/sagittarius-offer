<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UploadGunPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480', 'dimensions:min_width=1,min_height=1'],
            'mode' => ['sometimes', 'required', 'in:append,replace'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.required' => 'Prześlij co najmniej jedno zdjęcie.',
            'photos.array' => 'Zdjęcia należy przesłać jako listę plików photos[].',
            'photos.min' => 'Prześlij co najmniej jedno zdjęcie.',
            'photos.max' => 'Broń może mieć maksymalnie 10 zdjęć.',
            'photos.*.required' => 'Plik zdjęcia jest wymagany.',
            'photos.*.file' => 'Prześlij plik zdjęcia.',
            'photos.*.image' => 'Plik musi być poprawnym zdjęciem.',
            'photos.*.mimes' => 'Dozwolone formaty zdjęć: JPG, PNG i WebP.',
            'photos.*.max' => 'Zdjęcie może mieć maksymalnie 20 MB.',
            'photos.*.dimensions' => 'Plik musi zawierać poprawny obraz.',
            'mode.required' => 'Podaj tryb append lub replace.',
            'mode.in' => 'Dozwolone tryby to append i replace.',
        ];
    }

    /** @return array<int, UploadedFile> */
    public function getPhotos(): array
    {
        return $this->validated('photos');
    }

    public function replacesPhotos(): bool
    {
        return $this->validated('mode', 'append') === 'replace';
    }
}
