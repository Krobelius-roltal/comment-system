<?php

namespace App\Http\Requests;

use App\Data\News\CreateNewsData;
use Illuminate\Foundation\Http\FormRequest;

class CreateNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return CreateNewsData::getValidationRules([]);
    }

    public function toDto(): CreateNewsData
    {
        return CreateNewsData::from($this->validated());
    }
}
