@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard</h1>
    <div class="text-muted">{{ now()->format('l, d F Y') }}</div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Today's Orders</h6>
                        <h3 class="mb-0">{{ $todayOrders }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Today's Revenue</h6>
                        <h3 class="mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Pending Orders</h6>
                        <h3 class="mb-0">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Low Stock Items</h6>
                        <h3 class="mb-0">{{ $lowStockItems }}</h3>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Table</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->table->table_number }}</td>
                                <td>{{ $order->customer_name ?: 'Guest' }}</td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'selesai' ? 'success' : ($order->status === 'dibatalkan' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Menus -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Popular Menus</h5>
            </div>
            <div class="card-body">
                @forelse($popularMenus as $menu)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-1">{{ $menu->name }}</h6>
                        <small class="text-muted">{{ $menu->order_items_count }} orders</small>
                    </div>
                    <span class="badge bg-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-muted text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection