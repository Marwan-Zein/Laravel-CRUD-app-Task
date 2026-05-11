<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paginate' => ['boolean','sometimes'],
            'per_page' => ['whenpaginate', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'like' => ['sometimes', 'string']
        ];
    }

    public function getPerPage(): int
    {
        return $this->input('per_page', 10);
    }

    public function getPage(): int
    {
        return $this->input('page', 1);
    }

    public function getSearch(): ?string
    {
        return $this->input('like');
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('paginate')) {
            $this->merge([
                'paginate' => filter_var($this->paginate, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }
    }
}
