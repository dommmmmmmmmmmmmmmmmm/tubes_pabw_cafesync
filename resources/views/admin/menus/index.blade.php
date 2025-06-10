@extends('layouts.admin')

@section('title', 'Menus')

@push('styles')
<style>
    .menu-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .menu-image {
        position: relative;
        overflow: hidden;
    }

    .menu-image img {
        transition: transform 0.3s ease;
    }

    .menu-card:hover .menu-image img {
        transform: scale(1.05);
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
    }

    .price-tag {
        font-size: 1.25rem;
        font-weight: 700;
        color: #28a745;
    }

    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    .filter-card .form-select,
    .filter-card .form-control {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-action {
        width: 35px;
        height: 35px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .empty-state {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 15px;
        padding: 3rem;
    }

    .category-badge {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<!-- Header Section -->
<div class="header-section">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-utensils me-3"></i>Menu Management
            </h1>
            <p class="mb-0 opacity-75">Manage your restaurant menu items</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="btn btn-light btn-lg">
            <i class="fas fa-plus me-2"></i>Add New Menu
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-utensils fa-2x mb-2"></i>
                <h4>{{ $menus->total() }}</h4>
                <small>Total Menus</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $menus->where('stock', '>', 10)->count() }}</h4>
                <small>In Stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4>{{ $menus->where('stock', '<=', 10)->where('stock', '>', 0)->count() }}</h4>
                <small>Low Stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <h4>{{ $menus->where('stock', 0)->count() }}</h4>
                <small>Out of Stock</small>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Filters -->
<div class="card filter-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-tags me-1"></i>Category
                </label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">
                    <i class="fas fa-box me-1"></i>Stock Status
                </label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">
                    <i class="fas fa-search me-1"></i>Search
                </label>
                <input type="text" name="search" class="form-control" placeholder="Search menus..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-light w-100">
                    <i class="fas fa-filter me-1"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced Menu Grid -->
<div class="row">
    @forelse($menus as $menu)
    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
        <div class="card menu-card h-100">
            <div class="menu-image position-relative">
                @if($menu->image)
                <img src="{{ Storage::url($menu->image) }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                @else
                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" style="height: 220px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    <i class="fas fa-image fa-3x text-muted"></i>
                </div>
                @endif

                <div class="stock-badge">
                    <span class="badge bg-{{ $menu->isOutOfStock() ? 'danger' : ($menu->isLowStock() ? 'warning' : 'success') }} shadow">
                        {{ $menu->stock }} left
                    </span>
                </div>
            </div>

            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-1 text-truncate">{{ $menu->name }}</h5>
                    <span class="category-badge">{{ $menu->category->name }}</span>
                </div>

                <p class="card-text text-muted small flex-grow-1" style="line-height: 1.4;">
                    {{ Str::limit($menu->description, 90) }}
                </p>

                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="price-tag">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </div>
                        @if($menu->isOutOfStock())
                        <span class="badge bg-danger">Out of Stock</span>
                        @elseif($menu->isLowStock())
                        <span class="badge bg-warning text-dark">Low Stock</span>
                        @else
                        <span class="badge bg-success">Available</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('admin.menus.show', $menu) }}"
                            class="btn btn-outline-info btn-action"
                            title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.menus.edit', $menu) }}"
                            class="btn btn-outline-warning btn-action"
                            title="Edit Menu">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-outline-danger btn-action"
                                title="Delete Menu"
                                onclick="return confirm('Are you sure you want to delete this menu item?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state text-center">
            <div class="mb-4">
                <i class="fas fa-utensils" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h3 class="text-muted mb-3">No Menu Items Found</h3>
            <p class="text-muted mb-4 lead">
                @if(request()->hasAny(['category', 'status', 'search']))
                No menu items match your current filters. Try adjusting your search criteria.
                @else
                You haven't created any menu items yet. Start building your menu now!
                @endif
            </p>
            @if(request()->hasAny(['category', 'status', 'search']))
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </a>
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add New Menu
                </a>
            </div>
            @else
            <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Create Your First Menu Item
            </a>
            @endif
        </div>
    </div>
    @endforelse
</div>

<!-- Enhanced Pagination -->
@if($menus->hasPages())
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Showing {{ $menus->firstItem() }} to {{ $menus->lastItem() }} of {{ $menus->total() }} results
            </div>
            <div>
                {{ $menus->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading state to filter form
        const filterForm = document.querySelector('form');
        const filterButton = filterForm.querySelector('button[type="submit"]');

        filterForm.addEventListener('submit', function() {
            filterButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
            filterButton.disabled = true;
        });

        // Add confirmation for delete actions
        const deleteButtons = document.querySelectorAll('button[onclick*="confirm"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This menu item will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    });
</script>
@endpush