<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return view('product.index');
    }

    public function create(){
        return view('product.create');
    }

    public function store(Request $request){
        dd($request->all());
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 401);
        }
        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
        foreach ($request->image as $key => $value) {

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time(). '.' . $file->getClientOriginalExtension();
                $file->move(public_path('product'), $filename);
                $product->image = $filename;
                $product->save();
            }

        }

        return response()->json(['status' => 'success', 'message' => 'Product created successfully'], 200);
    }

    public function show($id){
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        return view('product.show', compact('product'));
    }

    public function edit($id){
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        return view('product.edit', compact('product'));
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 401);
        }

        $product = Product::find($request->id);

        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }

        Product::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('image')) {
            if (file_exists(public_path('product/' . $product->image))) {
                unlink(public_path('product/' . $product->image));
            }
            $file = $request->file('image');
            $filename = time(). '.' . $file->getClientOriginalExtension();
            $file->move(public_path('product'), $filename);
            $product->images()->create(['image' => $filename]);
        }

        return response()->json(['status' => 'success', 'message' => 'Product created successfully'], 200);
    }

    public function delete($id){
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        if (file_exists(public_path('product/' . $product->image))) {
            unlink(public_path('product/' . $product->image));
        }
        $product->delete();
        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully'], 200);
    }
}
