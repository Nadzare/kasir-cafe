<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display transaction receipt by UUID
     * 
     * @param string $uuid
     * @return \Illuminate\View\View
     */
    public function show($uuid)
    {
        // Find transaction by UUID with relationships
        $transaction = Transaction::with([
            'cashier',           // User yang melakukan transaksi
            'items.product'      // Items dengan detail produk
        ])->where('uuid', $uuid)->first();
        
        // Return 404 if transaction not found
        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan');
        }
        
        // Return view with transaction data
        return view('struk', [
            'transaction' => $transaction
        ]);
    }
}
