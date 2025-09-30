<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Interfaces\IInvoiceRepository;

class InvoiceRepository implements IInvoiceRepository
{
    public function all(): array
    {
        return Invoice::all()->toArray();
    }

    public function find(int $id): Invoice
    {
        return Invoice::findOrFail($id);
    }

    public function create(array $data): Invoice
    {
        $model = Invoice::create($data);
        $model->products()->attach($data['products']);
        return $model;
    }

    public function update(int $id, array $data): Invoice
    {
        $model = Invoice::findOrFail($id);
        $model->update($data);
        $model->products()->sync($data['products']);
        return $model;
    }

    public function delete(int $id): bool
    {
        $model = Invoice::findOrFail($id);
        if (!$model) {
            return false;
        }
        return (bool) $model->delete();
    }
}
