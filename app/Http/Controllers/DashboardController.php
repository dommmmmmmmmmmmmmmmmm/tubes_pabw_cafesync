<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todayOrders = Order::today()->count();
        $todayRevenue = Order::today()->where('payment_status', 'paid')->sum('total');
        $pendingOrders = Order::where('status', 'menunggu_konfirmasi')->count();
        $lowStockItems = Menu::lowStock()->count();
        
        $recentOrders = Order::with(['table', 'items.menu'])
            ->latest()
            ->take(10)
            ->get();
            
        $popularMenus = Menu::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders', 'todayRevenue', 'pendingOrders', 
            'lowStockItems', 'recentOrders', 'popularMenus'
        ));
    }
}
