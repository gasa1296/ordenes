<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

interface IProductRepository
{
    public function all(): Collection;

    public function find($id): ?Product;

    public function create(array $data): ?Product;

    public function update($id, array $data): ?Product;

    public function delete($id): bool;
}
