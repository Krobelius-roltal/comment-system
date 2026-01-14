<?php

namespace App\Http\Requests;

use App\Data\VideoPost\UpdateVideoPostData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoPostRequest extends FormRequest
{
    public function rules(): array
    {
        return UpdateVideoPostData::getValidationRules([]);
    }

    public function toDto(): UpdateVideoPostData
    {
        return UpdateVideoPostData::from($this->validated());
    }
}
