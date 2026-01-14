<?php

namespace App\Http\Requests;

use App\Data\Common\PaginationData;
use Illuminate\Foundation\Http\FormRequest;

class ShowNewsRequest extends FormRequest
{
    public function rules(): array
    {
        return PaginationData::getValidationRules([]);
    }

    public function toDto(): PaginationData
    {
        $data = $this->validated();
        if (!isset($data['cursor']) && $this->has('cursor')) {
            $data['cursor'] = (int) $this->input('cursor');
        }
        if (!isset($data['limit']) && $this->has('limit')) {
            $data['limit'] = (int) $this->input('limit', 10);
        }

        return PaginationData::from($data);
    }
}
