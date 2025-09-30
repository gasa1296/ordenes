<?php

namespace App\Repositories\Interfaces;

use App\Models\Invoice;

interface IInvoiceRepository
{
    public function all(): array;

    public function find(int $id): Invoice;

    public function create(array $data): Invoice;

    public function update(int $id, array $data): Invoice;

    public function delete(int $id): bool;
}
