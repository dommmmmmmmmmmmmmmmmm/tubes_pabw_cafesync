<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Menu;
use App\Models\Category;

class CustomerController extends Controller
{
    public function getTable(Table $table)
    {
        return response()->json([
            'status' => 'success',
            'data' => $table
        ]);
    }

    public function getMenus()
    {
        $menus = Menu::with('category')
            ->available()
            ->get()
            ->groupBy('category.name');

        return response()->json([
            'status' => 'success',
            'data' => $menus
        ]);
    }

    public function getCategories()
    {
        $categories = Category::active()
            ->withCount(['menus' => function($query) {
                $query->available();
            }])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
