<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

class TestController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $products = $this->service->loadData();
        return view('products.index', compact('products'));
    }

    public function saveOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|string',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $products = $this->service->saveOrUpdate($validated);

        return response()->json($products);
    }
}
