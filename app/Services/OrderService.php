<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Menu;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function processOrder(array $orderData)
    {
        return DB::transaction(function () use ($orderData) {
            // Validate stock availability
            foreach ($orderData['items'] as $item) {
                $menu = Menu::find($item['menu_id']);
                if (!$menu || $menu->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$menu->name}");
                }
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($orderData['items'] as $item) {
                $menu = Menu::find($item['menu_id']);
                $subtotal += $menu->price * $item['quantity'];
            }

            $tax = $subtotal * 0.1;
            $total = $subtotal + $tax;

            // Create order
            $order = Order::create([
                'table_id' => $orderData['table_id'],
                'customer_name' => $orderData['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $orderData['payment_method'],
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Create order items
            foreach ($orderData['items'] as $item) {
                $menu = Menu::find($item['menu_id']);
                $order->items()->create([
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price,
                    'total' => $menu->price * $item['quantity'],
                ]);
            }

            return $order;
        });
    }

    public function confirmOrder(Order $order)
    {
        if ($order->status !== 'menunggu_konfirmasi') {
            throw new \Exception('Order cannot be confirmed');
        }

        DB::transaction(function () use ($order) {
            // Update stock
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
                    'reason' => "Order #{$order->order_number}",
                ]);
            }

            // Update order status
            $order->update([
                'status' => 'sedang_diproses',
                'confirmed_at' => now(),
            ]);
        });

        return $order;
    }
}