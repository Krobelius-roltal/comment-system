<?php

namespace App\Http\Requests;

use App\Data\Comment\CreateCommentData;
use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return CreateCommentData::getValidationRules([]);
    }

    public function toDto(): CreateCommentData
    {
        return CreateCommentData::from($this->validated());
    }
}
