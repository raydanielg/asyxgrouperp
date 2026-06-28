<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function salesInvoices()
    {
        return response()->json(SalesInvoice::with('company', 'items')->latest()->paginate(20));
    }

    public function salesInvoiceShow(SalesInvoice $invoice)
    {
        return response()->json($invoice->load('company', 'items'));
    }

    public function purchaseInvoices()
    {
        return response()->json(PurchaseInvoice::with('company', 'items')->latest()->paginate(20));
    }

    public function purchaseInvoiceShow(PurchaseInvoice $invoice)
    {
        return response()->json($invoice->load('company', 'items'));
    }
}
