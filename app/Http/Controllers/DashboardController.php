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
            'totalSales' => Order::where('status', 'completed')->sum('grand_total'),
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

    public function revenueChart()
    {
        $revenue = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereYear('orders.created_at', now()->year)
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.price * order_items.quantity) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = (float) ($revenue[$i] ?? 0);
        }
        
        return response()->json($monthlyRevenue);
    }
}
