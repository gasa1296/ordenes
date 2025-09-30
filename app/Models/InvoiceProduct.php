<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $table = 'invoice_product';
    public $timestamps = false;
    protected $fillable = ['invoice_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
