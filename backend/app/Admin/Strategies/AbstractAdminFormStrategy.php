<?php

namespace App\Admin\Strategies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class AbstractAdminFormStrategy implements AdminFormStrategy
{
    abstract protected function rules(Request $request, ?Model $model = null): array;

    protected function messages(): array
    {
        return [];
    }

    public function validateData(Request $request, ?Model $model = null): array
    {
        $rules = $this->rules($request, $model);
        $field = (string) $request->input('_validate_field', '');

        if ($field !== '' && array_key_exists($field, $rules)) {
            $rules = [$field => $rules[$field]];
        } elseif ($field !== '') {
            $rules = [];
        }

        return Validator::make($request->all(), $rules, $this->messages())->validate();
    }

    public function update(Request $request, Model $model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    public function delete(Request $request, Model $model): void
    {
        $model->delete();
    }
}

