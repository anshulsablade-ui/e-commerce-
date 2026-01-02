<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::all();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order', function ($row) {
                    return $row->order_number ?? 'N/A';
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->name ?? 'N/A';
                })
                ->addColumn('subtotal', function ($row) {
                    return $row->subtotal ?? 'N/A';
                })
                ->addColumn('descount', function ($row) {
                    return $row->discount ?? 'N/A';
                })
                ->addColumn('total', function ($row) {
                    return $row->total ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center">
                    <a href="' . route('customer.show', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-show"></i></a>
                    <a href="' . route('customer.edit', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                    <a href="javascript:void(0)" data-id="' . $row->id . '" class="menu-link delete"><i class="menu-icon text-danger tf-icons bx bx-trash"></i></a>
                </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('order.index');
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $categories = Category::all();
        return view('order.create', compact('customers', 'products', 'categories'));
    }
}
