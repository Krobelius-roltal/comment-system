<?php

namespace App\Http\Requests;

use App\Data\Comment\CommentIndexData;
use Illuminate\Foundation\Http\FormRequest;

class IndexCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return CommentIndexData::getValidationRules([]);
    }

    public function toDto(): CommentIndexData
    {
        $data = $this->validated();
        if (!isset($data['parent_id']) && $this->has('parent_id')) {
            $data['parent_id'] = (int) $this->input('parent_id');
        }
        if (!isset($data['cursor']) && $this->has('cursor')) {
            $data['cursor'] = (int) $this->input('cursor');
        }
        if (!isset($data['limit']) && $this->has('limit')) {
            $data['limit'] = (int) $this->input('limit', 10);
        }

        return CommentIndexData::from($data);
    }
}
