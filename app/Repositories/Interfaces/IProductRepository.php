<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface IProductRepository
{
    public function all(): Collection;

    public function find(int $id): ?Product;

    public function create(array $data): ?Product;

    public function update(int $id, array $data): ?Product;

    public function delete(int $id): bool;
}
