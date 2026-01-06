<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalOrders' => Order::count(),
            'totalSales' => Order::sum('grand_total'),
            'customers' => Customer::count(),
            'products' => Product::count(),
        ]);
    }

    public function orderStatusChart()
    {
        $ordersStatus = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json($ordersStatus);
    }


    public function profitVsRevenue()
    {
        $data = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_items.total) as revenue'),
                DB::raw('SUM(order_items.price * order_items.quantity * 0.7) as cost')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($data->toArray());

        $result = $data->map(function ($row) {
            return [
                'month' => $row->month,
                'revenue' => round($row->revenue, 2),
                'profit' => round($row->revenue - $row->cost, 2),
            ];
        });

        return response()->json($result);
    }

}
