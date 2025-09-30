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

    public function find($id): ?Product
    {
        return Product::find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update($id, array $data): ?Product
    {
        $product = Product::find($id);
        if ($product) {
            $product->update($data);
        }
        return $product;
    }

    public function delete($id): bool
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return true;
        }
        return false;
    }
}
