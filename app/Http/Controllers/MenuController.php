<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'available':
                    $query->available();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menus = $query->paginate(12);
        $categories = Category::active()->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create($validated);

        // Record initial stock movement
        if ($validated['stock'] > 0) {
            StockMovement::create([
                'menu_id' => $menu->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $validated['stock'],
                'stock_before' => 0,
                'stock_after' => $validated['stock'],
                'reason' => 'Initial stock'
            ]);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully');
    }

    public function show(Menu $menu)
    {
        $menu->load(['category', 'stockMovements.user']);
        $stockMovements = $menu->stockMovements()->latest()->paginate(10);
        
        return view('admin.menus.show', compact('menu', 'stockMovements'));
    }

    public function edit(Menu $menu)
    {
        $categories = Category::active()->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        // Track stock changes
        $oldStock = $menu->stock;
        $newStock = $validated['stock'];

        $menu->update($validated);

        // Record stock movement if stock changed
        if ($oldStock != $newStock) {
            $type = $newStock > $oldStock ? 'in' : 'out';
            $quantity = abs($newStock - $oldStock);

            StockMovement::create([
                'menu_id' => $menu->id,
                'user_id' => Auth::id(),
                'type' => $type === 'in' ? 'in' : 'adjustment',
                'quantity' => $quantity,
                'stock_before' => $oldStock,
                'stock_after' => $newStock,
                'reason' => 'Stock adjustment from menu update'
            ]);
        }

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->orderItems()->exists()) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Cannot delete menu with existing orders');
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully');
    }

    public function updateStock(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255'
        ]);

        $oldStock = $menu->stock;
        
        switch ($validated['type']) {
            case 'in':
                $newStock = $oldStock + $validated['quantity'];
                break;
            case 'out':
                $newStock = max(0, $oldStock - $validated['quantity']);
                break;
            case 'adjustment':
                $newStock = $validated['quantity'];
                break;
        }

        $menu->update(['stock' => $newStock]);

        StockMovement::create([
            'menu_id' => $menu->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'stock_before' => $oldStock,
            'stock_after' => $newStock,
            'reason' => $validated['reason'] ?? 'Manual stock update'
        ]);

        return redirect()->back()
            ->with('success', 'Stock updated successfully');
    }
}
