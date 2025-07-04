<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line as EloquentLine;
use App\Models\Domain\Entities\Line;

class EloquentLineRepository implements LineRepository
{
    public function create(array $attributes): Line
    {
        return EloquentLine::create($attributes);
    }

    public function findById(string $id): ?Line
    {
        return EloquentLine::find($id);
    }

    public function update(string $id, array $attributes): Line
    {
        $line = EloquentLine::findOrFail($id);
        $line->update($attributes);
        return $line;
    }

    public function delete(string $id): void
    {
        EloquentLine::destroy($id);
    }

    public function all($sortField = 'mobile_number', $sortDirection = 'asc'): array
    {
        return EloquentLine::with('branch')->orderBy($sortField, $sortDirection)->get()->all();
    }
} 