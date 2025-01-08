<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  // Ensure only authenticated users can access the methods
    }

    // List all products with AJAX search functionality
    public function index(Request $request)
    {
        $query = Product::where('user_id',Auth::user()->id);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);  // Paginate results for AJAX search
        if(empty($products)){
            return response()->json(['message' => 'products not found']);
        }

        return response()->json($products);
    }

    // Create or update a product
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->user_id = Auth::user()->id;
        $product->save();

        return response()->json($product, 201);
    }

    public function update(Request $request,$id)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $product = Product::where('user_id',Auth::user()->id)->find($id);
        if($product){
            $product->name = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->user_id = Auth::user()->id;
            $product->save();
            return response()->json($product, 201);
        }else{
            return response()->json(['message' => 'Product not found']);
        }
    }

    // Generate random products via Artisan command
    public function generateRandomProducts()
    {
        Product::factory()->count(10)->create();
        return response()->json(['message' => 'Random products generated successfully']);
    }

    public function delete($id)
    {
        $product = Product::where('user_id',Auth::user()->id)->find($id);
        
        if($product){
            $product->delete();
            return response()->json(['message' => 'Products Delete successfully']);
        }else{
            return response()->json(['message' => 'Somthing Went Wrong']);
        }
    }
}
