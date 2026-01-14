<?php

namespace App\Http\Requests;

use App\Data\VideoPost\CreateVideoPostData;
use Illuminate\Foundation\Http\FormRequest;

class CreateVideoPostRequest extends FormRequest
{
    public function rules(): array
    {
        return CreateVideoPostData::getValidationRules([]);
    }

    public function toDto(): CreateVideoPostData
    {
        return CreateVideoPostData::from($this->validated());
    }
}
