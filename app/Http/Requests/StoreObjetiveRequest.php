<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreObjetiveRequest extends FormRequest
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
            'period' => 'required|date',
            'compare_period' => [
                'required',
                'date',
            ],
            'compare_period_secondary' => [
                'required',
                'date',
            ],
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
            'period.required' => 'El campo periodo es requerido',
            'period.date' => 'El campo periodo debe ser una fecha',
            'compare_period.required' => 'El campo periodo de comparación es requerido',
            'compare_period.date' => 'El campo periodo de comparación debe ser una fecha',
            'compare_period.exists' => 'El periodo de comparación no existe en la tabla sell_outs',
            'compare_period_secondary.required' => 'El campo periodo de comparación secundario es requerido',
            'compare_period_secondary.date' => 'El campo periodo de comparación secundario debe ser una fecha',
            'compare_period_secondary.exists' => 'El periodo de comparación secundario no existe en la tabla sell_outs',
        ];
    }
}
