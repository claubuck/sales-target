<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePercentageRequest extends FormRequest
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
            'objetive_id' => 'required|integer',
            'brand' => 'required|string',
            'percentage' => 'required|numeric',
            'scope' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages(): array
    {
        return [
            'objetive_id.required' => 'El campo objetivo es requerido',
            'objetive_id.integer' => 'El campo objetivo debe ser un número entero',
            
            'brand.required' => 'El campo marca es requerido',
            'brand.string' => 'El campo marca debe ser una cadena de texto',

            'percentage.required' => 'El campo porcentaje es requerido',
            'percentage.numeric' => 'El campo porcentaje debe ser un número',

            'scope.required' => 'El campo alcance es requerido',
            'scope.string' => 'El campo alcance debe ser una cadena de texto',
        ];
    }
}
