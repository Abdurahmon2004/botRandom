<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::paginate(10);
        if($request->ajax()){
            return view('dashboard.products.ajax-table', compact('products'));
        }
        return view('dashboard.products.index', compact('products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        Product::create([
            'name'=>$request->name,
            'status'=>$request->status??0
        ]);
        return response()->json(['success' => 'Product added successfully']);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $product = Product::findOrFail($id);
        $product->update([
            'name'=>$request->name,
            'status'=>$request->status??0
        ]);
        return response()->json(['success' => 'Product updated successfully']);
    }
}
