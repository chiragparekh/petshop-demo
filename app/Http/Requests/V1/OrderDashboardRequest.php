<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderDashboardRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['required', 'integer', 'numeric'],
            'limit' => ['required', 'integer', 'numeric'],
            'sortBy' => ['nullable', 'string', Rule::in(['uuid', 'status', 'customer', 'ordered_products', 'amount'])],
            'desc' => ['required', 'boolean'],
            'dateRange' => [Rule::requiredIf(function() {
                return ! $this->get('fixRange', null);
            }), 'array'],
            'dateRange.from' => [Rule::requiredIf(function() {
                return ! $this->get('fixRange', null);
            }), 'date', 'date_format:Y-m-d'],
            'dateRange.to' => [Rule::requiredIf(function() {
                return ! $this->get('fixRange', null);
            }), 'date', 'date_format:Y-m-d', 'after_or_equal:dateRange.from'],
            'fixRange' => [Rule::requiredIf(function() {
                return ! $this->get('dateRange', null);
            }), 'in:today,monthly,yearly'],
        ];
    }
}
