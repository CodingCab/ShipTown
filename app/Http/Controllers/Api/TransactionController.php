<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $attributes = $request->all();

        $transaction = Transaction::create($attributes);

        return response()->json($transaction, 201);
    }
}
