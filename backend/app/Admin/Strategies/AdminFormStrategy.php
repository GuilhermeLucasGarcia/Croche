<?php

namespace App\Admin\Strategies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface AdminFormStrategy
{
    public function key(): string;

    public function singularLabel(): string;

    public function pluralLabel(): string;

    public function listQuery(Request $request): Builder;

    public function listColumns(): array;

    public function fields(Request $request, ?Model $model = null): array;

    public function validateData(Request $request, ?Model $model = null): array;

    public function load(Request $request, string $id): Model;

    public function create(Request $request, array $data): Model;

    public function update(Request $request, Model $model, array $data): Model;

    public function delete(Request $request, Model $model): void;
}
