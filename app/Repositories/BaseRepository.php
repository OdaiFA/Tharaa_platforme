<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    abstract protected function model(): string;

    public function query(?int $userId = null): Builder
    {
        $query = (new ($this->model()))->newQuery();

        return $userId !== null ? $query->where('user_id', $userId) : $query;
    }

    public function find(int $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function findForUser(int $id, int $userId): ?Model
    {
        return $this->query($userId)->find($id);
    }

    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->fresh();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function paginate(int $perPage = 15, ?int $userId = null): LengthAwarePaginator
    {
        return $this->query($userId)->latest()->paginate($perPage);
    }
}
