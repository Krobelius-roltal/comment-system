<?php

namespace App\Http\Requests;

use App\Data\Comment\UpdateCommentData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return UpdateCommentData::getValidationRules([]);
    }

    public function toDto(): UpdateCommentData
    {
        return UpdateCommentData::from($this->validated());
    }
}
