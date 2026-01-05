<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('category');

            if ($request->category_id) {
                $products->where('category_id', $request->category_id);
            }

            if (!is_null($request->status)) {
                $products->where('status', $request->status);
            }


            if ($request->stock == 'out_of_stock') {
                $products->where('stock', 0);
            }
            if ($request->stock == 'in_stock') {
                $products->where('stock', '>', 0);
            }

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('price', function ($row) {
                    return '₹' . $row->price ?? 'N/A';
                })
                ->addColumn('image', function ($row) {
                    $data = $row->images->first()->image ?? 'no-image.png';
                    return asset('product/' . $data);
                })
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center">
                    <a href="' . route('product.show', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-show"></i></a>
                    <a href="' . route('product.edit', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="menu-link delete"><i class="menu-icon text-danger tf-icons bx bx-trash"></i></a>
                </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $categories = Category::select('id', 'name')->get();

        return view('product.index', compact('categories'));
    }

    public function slug(Request $request)
    {
        $slug = Product::where('slug', $request->slug)->first();
        if ($slug) {
            return response()->json(['status' => 'error', 'message' => 'Slug already exists'], 422);
        }
        return response()->json(['status' => 'success', 'message' => 'Slug is available'], 200);
    }

    public function create()
    {
        $categories = Category::all();
        return view('product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'category' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status,
                'category_id' => $request->category,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {

                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('product'), $filename);
                    $product->images()->create([
                        'image' => $filename
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Product created successfully');
            return response()->json(['status' => 'success', 'message' => 'Product created successfully'], 201);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        return view('product.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'category'])->find($id);

        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        $categories = Category::all();
        return view('product.update', compact('product', 'categories'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'category' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        $product = Product::find($request->id);

        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }

        DB::beginTransaction();

        try {
            $product = Product::where('id', $request->id)->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status,
                'category_id' => $request->category,
            ]);

            if ($request->hasFile('images')) {
                $product = Product::with('images')->find($request->id);

                // delete old images
                if ($product->images) {
                    foreach ($product->images as $image) {
                        if (file_exists(public_path('product/' . $image->image))) {
                            unlink(public_path('product/' . $image->image));
                        }
                        $image->delete();
                    }
                }

                // insert new images
                foreach ($request->file('images') as $image) {

                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('product'), $filename);
                    $product->images()->create([
                        'image' => $filename
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Product updated successfully');
            return response()->json(['status' => 'success', 'message' => 'Product updated successfully'], 201);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }

    }

    public function delete($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['errors' => 'Product not found.', 'status' => 'errors']);
        }
        $images = $product->images;
        foreach ($images as $image) {
            if (file_exists(public_path('product/' . $image->image))) {
                unlink(public_path('product/' . $image->image));
            }
            $image->delete();
        }
        $product->delete();

        session()->flash('success', 'Product deleted successfully');
        return response()->json(['status' => 'success', 'message' => 'Product deleted successfully'], 200);
    }

    public function ajaxProduct(Request $request)
    {

        $products = Product::where('category_id', $request->category_id)
            ->where('name', 'LIKE', "%{$request->search}%")
            ->select('id', 'name', 'price')
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
