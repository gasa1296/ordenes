<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
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
     * Display the specified resource.
     */
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load('products');
        return (new InvoiceResource($invoice))->response();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $model = $this->invoiceRepository->create($inputs);
        $model->load('products');
        return (new InvoiceResource($model))->response();
    }
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $inputs = $request->validated();
        $prods = [];
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Cannot update a paid invoice.'], 400);
        }
        if (!isset($inputs['products'])) {
            $inputs['products'] = [];
        }
        foreach ($inputs['products'] as $product) {
            $prods[$product['id']] = ['quantity' => $product['quantity']];
        }
        $inputs['products'] = $prods;
        $inputs['status'] = 'paid';
        $model = $this->invoiceRepository->update($invoice->id, $inputs);
        $total = $model->products->sum(function($product) {
            return $product->price * $product->pivot->quantity;
        });
        $model = $this->invoiceRepository->update($invoice->id, ['total' => $total]);
        $model->load('products');
        return (new InvoiceResource($model))->response();
    }
}
