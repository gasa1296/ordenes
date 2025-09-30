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
        $inputs = $request->validated();
        $prods = [];
        foreach ($inputs['products'] as $product) {
            $prods[$product['id']] = ['quantity' => $product['quantity']];
        }
        $inputs['products'] = $prods;
        $model = $this->invoiceRepository->create($inputs);
        $model->load('products');
        return (new InvoiceResource($model))->response();
    }
}
