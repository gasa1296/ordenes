<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IProductRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

class ProductRepository implements IProductRepository
{
    public function all(): Collection
    {
        return Product::all();
    }

    public function find(int $id): ?Product
    {
        return Product::findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): ?Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);
        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }
}
