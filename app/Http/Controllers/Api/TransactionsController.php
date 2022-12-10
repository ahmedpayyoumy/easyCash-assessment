<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index(Request $request) :array
    {
        return (new \App\Models\Api\Transaction)->getAllTransactions($request->all());
    }
}
