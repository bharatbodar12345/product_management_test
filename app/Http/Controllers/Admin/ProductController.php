<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // List all products (for DataTables view)
    public function index()
    {
        return view('admin.products.index');
    }

    // Get products for DataTables
    public function data(Request $request)
    {
        if(Auth::user()->is_admin == 1){
            $products = Product::get();
        }else{
            $products = Product::where('user_id',Auth::user()->id)->get();
        }

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('action', function ($product) {
                // return view('admin.products.action', compact('product'));
                $btn = '<button class="btn btn-warning edit-btn" data-id="'.$product->id.'">Edit</button><button class="btn btn-danger delete-btn" data-id="'.$product->id.'">Delete</button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    // Store a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function getByid(Request $request,$id)
    {
    
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }
    // Update an existing product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => Auth::user()->id,
        ]);

        return response()->json(['message' => 'Product updated successfully']);
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
