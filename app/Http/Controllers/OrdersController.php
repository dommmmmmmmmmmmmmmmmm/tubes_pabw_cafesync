<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,qris,e_wallet,mobile_banking',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $menu = Menu::find($item['menu_id']);
                
                if (!$menu->is_available || $menu->stock < $item['quantity']) {
                    throw new \Exception("Menu {$menu->name} not available or insufficient stock");
                }

                $itemTotal = $menu->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price,
                    'total' => $itemTotal
                ];
            }

            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;

            // Create order
            $order = Order::create([
                'table_id' => $validated['table_id'],
                'customer_name' => $validated['customer_name'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cash' ? 'pending' : 'pending',
                'notes' => $validated['notes'] ?? null
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $order->load(['table', 'items.menu'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.menu']);
        
        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function updatePayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $order->update(['payment_proof' => $path]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Payment proof uploaded successfully'
        ]);
    }
}
