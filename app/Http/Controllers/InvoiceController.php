<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Resources\Invoices\InvoiceResource;
use App\Repositories\Interfaces\IInvoiceRepository;

class InvoiceController extends Controller
{
    public function __construct(
        private IInvoiceRepository $invoiceRepository,
    )
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $model = $this->invoiceRepository->create($request->validated());
        $model->load('products');
        return (new InvoiceResource($model))->response();
    }
}
