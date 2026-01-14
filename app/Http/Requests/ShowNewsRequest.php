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
        if (!isset($data['offset']) && $this->has('offset')) {
            $data['offset'] = (int) $this->input('offset', 0);
        }
        if (!isset($data['limit']) && $this->has('limit')) {
            $data['limit'] = (int) $this->input('limit', 10);
        }

        return PaginationData::from($data);
    }
}
