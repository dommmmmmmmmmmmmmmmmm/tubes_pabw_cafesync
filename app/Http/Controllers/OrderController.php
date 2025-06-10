<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'items.menu']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('table')) {
            $query->whereHas('table', function($q) use ($request) {
                $q->where('table_number', 'like', '%' . $request->table . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu_konfirmasi,sedang_diproses,siap_diantar,selesai,dibatalkan'
        ]);

        if (!$order->canUpdateStatus()) {
            return redirect()->back()
                ->with('error', 'Cannot update status for this order');
        }

        // Store old status before update
        $oldStatus = $order->status;
        
        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'selesai') {
            $order->update(['completed_at' => now()]);
        }

        // Reduce stock when order is confirmed
        if ($validated['status'] === 'sedang_diproses' && $oldStatus === 'menunggu_konfirmasi') {
            foreach ($order->items as $item) {
                $menu = $item->menu;
                $stockBefore = $menu->stock;
                $newStock = max(0, $menu->stock - $item->quantity);
                $menu->update(['stock' => $newStock]);

                // Record stock movement
                StockMovement::create([
                    'menu_id' => $menu->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $newStock,
                    'reason' => "Order #{$order->order_number}"
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Order status updated successfully');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order->update($validated);

        return redirect()->back()
            ->with('success', 'Payment status updated successfully');
    }
}