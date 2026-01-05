<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::all();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('total_product', function ($row) {
                    return $row->products->count();
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center">
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="menu-link edit"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="menu-link delete"><i class="menu-icon text-danger tf-icons bx bx-trash"></i></a>
                </div>';
                })
                ->rawColumns(['total_product', 'action'])
                ->make(true);
        }
        return view('category.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }
        Category::create([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        session()->flash('success', 'Category created successfully');
        return response()->json(['status' => 'success', 'message' => 'Category created successfully'], 200);
    }

    public function edit($id)
    {
        $category = Category::select('id', 'name', 'slug')->find($id);
        return response()->json(['status' => 'success', 'data' => $category], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $request->id,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $request->id,
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }
        Category::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        session()->flash('success', 'Category updated successfully');
        return response()->json(['status' => 'success', 'message' => 'Category updated successfully'], 200);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['errors' => 'Category not found.', 'status' => 'errors']);
        }
        $category->delete();

        session()->flash('success', 'Category deleted successfully');
        return response()->json(['status' => 'success', 'message' => 'Category deleted successfully'], 200);
    }
}
