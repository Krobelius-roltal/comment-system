<?php

namespace App\Http\Requests;

use App\Data\News\UpdateNewsData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return UpdateNewsData::getValidationRules([]);
    }

    public function toDto(): UpdateNewsData
    {
        return UpdateNewsData::from($this->validated());
    }
}
