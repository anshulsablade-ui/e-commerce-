<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::orderBy('id', 'desc')->get();
            if ($request->status) {
                $orders = $orders->where('status', $request->status);
            }
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_number', function ($row) {
                    return $row->order_number ?? 'N/A';
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->name ?? 'N/A';
                })
                ->addColumn('items', function ($row) {
                    return $row->orderItem->count() ?? 'N/A';
                })
                ->addColumn('grand_total', function ($row) {
                    return '₹' . $row->grand_total ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex justify-content-center">
                    <a href="' . route('order.show', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-show"></i></a>
                    <a href="' . route('order.edit', $row->id) . '" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
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
        $categories = Category::all();
        return view('order.create', compact('categories'));
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found.'], 404);
        }

        // product out of stock
        foreach ($request->product as $index => $productId) {
            $product = Product::find($productId);
            if (!$product) {
                return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
            }
            if ($product->stock < $request->quantity[$index]) {
                return response()->json(['status' => 'error', 'message' => 'Product ' . $product->name . ' is out of stock.'], 400);
            }
        }

        DB::beginTransaction();

        try {

            $subtotal = array_sum($request->total);

            $discountPercent = $request->discount ?? 0;
            $discountAmount = ($subtotal * $discountPercent) / 100;

            $grandTotal = $subtotal - $discountAmount;

            $order = Order::create([
                'order_number'    => 'ORD-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                'customer_id' => $request->customer_id,
                'subtotal' => $subtotal,
                'discount' => $request->discount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'status' => $request->order_status,
                'payment_status' => 'pending',
            ]);

            foreach ($request->product as $index => $productId) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'price' => $request->price[$index],
                    'quantity' => $request->quantity[$index],
                    'total' => $request->total[$index],
                ]);

                // Update product quantity
                $product = Product::find($productId);
                $product->stock -= $request->quantity[$index];
                $product->save();
            }

            DB::commit();

            session()->flash('success', 'Order created successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'order_id' => $order->order_number,
                'redirect' => route('order.show', $order->id)
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('customer', 'orderItem.product')->findOrFail($id);
        return view('order.show', compact('order'));
    }

    public function edit($id)
    {
        $categories = Category::all();
        $order = Order::with('customer', 'orderItem.product.category')->find($id);
        // dd($order->toArray());
        return view('order.update', compact('order', 'categories'));
    }

    public function update(orderRequest $request, $id)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $subtotal = array_sum($request->total);

            $discountPercent = $request->discount ?? 0;
            $discountAmount = ($subtotal * $discountPercent) / 100;

            $grandTotal = $subtotal - $discountAmount;

            $order->update([
                'customer_id' => $request->customer_id,
                'subtotal' => $subtotal,
                'discount' => $request->discount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'status' => $request->order_status,
                'payment_status' => 'pending',
            ]);

            $orderItems = $order->orderItem()->get();

            foreach ($orderItems as $orderItem) {

                $orderItem->delete();

                $product = Product::find($orderItem->product_id);
                $product->stock += $orderItem->quantity;
                $product->save();
            }


            foreach ($request->product as $index => $productId) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'price' => $request->price[$index],
                    'quantity' => $request->quantity[$index],
                    'total' => $request->total[$index],
                ]);

                // Update product quantity
                $product = Product::find($productId);
                $product->stock -= $request->quantity[$index];
                $product->save();
            }

            DB::commit();

            session()->flash('success', 'Order updated successfully');
            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['errors' => 'Order not found.', 'status' => 'errors']);
        }
        if ($order->orderItem) {
            foreach ($order->orderItem as $item) {
                $item->delete();
            }
        }
        $order->delete();

        session()->flash('success', 'Order deleted successfully');
        return response()->json(['status' => 'success', 'message' => 'Order deleted successfully'], 200);
    }

}
