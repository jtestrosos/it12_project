<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Barangay Health Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { color: #111; }
        body.bg-dark { color: #fff; }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            border-right: 1px solid #e9ecef;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            background-color: #f0f0f0;
            min-height: 100vh;
        }
        .header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
        }
        .inventory-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            border: none;
            transition: transform 0.2s ease;
        }
        .inventory-card:hover {
            transform: translateY(-2px);
        }
        .stock-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .stock-good {
            background-color: #28a745;
        }
        .stock-low {
            background-color: #ffc107;
        }
        .stock-out {
            background-color: #dc3545;
        }
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        /* Cards inherit theme text color */
        .inventory-card { color: inherit; }
        /* Dark mode surfaces */
        body.bg-dark .main-content { background-color: #151718; }
        body.bg-dark .sidebar { background: #131516; border-right-color: #2a2f35; }
        body.bg-dark .header { background: #1b1e20; border-bottom-color: #2a2f35; }
        body.bg-dark .inventory-card { background: #1e2124; color: #e6e6e6; box-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        body.bg-dark .table thead, body.bg-dark .table-light { background: #1a1f24 !important; color: #e6e6e6; }
        /* Headings and muted text visibility */
        h1, h2, h3, h4, h5, h6 { color: inherit; }
        body.bg-dark .text-muted, body.bg-dark small { color: #b0b0b0 !important; }
        /* Dark mode modal + form fields */
        body.bg-dark .modal-content { background: #1e2124; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-label { color: #e6e6e6; }
        body.bg-dark .modal-content .form-control,
        body.bg-dark .modal-content .form-select,
        body.bg-dark .form-control,
        body.bg-dark .form-select { background-color: #0f1316; color: #e6e6e6; border-color: #2a2f35; }
        body.bg-dark .modal-content .form-control::placeholder { color: #9aa4ad; }
    </style>
</head>
<body>
    <script>
        (function(){
            if (localStorage.getItem('app-theme') === 'dark') {
                document.body.classList.add('bg-dark','text-white');
            }
        })();
    </script>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ asset('images/malasakit-logo-blue.png') }}" alt="Logo" class="me-3" style="width: 52px; height: 52px;">
                            <div>
                                <h6 class="mb-0 fw-bold" style="letter-spacing: .5px;">MALASAKIT</h6>
                            </div>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-th-large me-2"></i> Dashboard
                            </a>
                            <a class="nav-link" href="{{ route('admin.patients') }}">
                                <i class="fas fa-user me-2"></i> Patient Management
                            </a>
                            <a class="nav-link" href="{{ route('admin.appointments') }}">
                                <i class="fas fa-calendar-check me-2"></i> Appointments
                            </a>
                            <a class="nav-link" href="{{ route('admin.reports') }}">
                                <i class="fas fa-chart-bar me-2"></i> Services & Reports
                            </a>
                            <a class="nav-link active" href="{{ route('admin.inventory') }}">
                                <i class="fas fa-box me-2"></i> Inventory
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <div class="main-content">
                    <!-- Header -->
                    <div class="header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Inventory Management</h4>
                            <p class="text-muted mb-0">Manage medical supplies and equipment</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link text-decoration-none text-muted me-2" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                                <i class="fas fa-moon"></i>
                            </button>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <small class="text-muted">Admin</small>
                                </div>
                            </div>
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary ms-3" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="fas fa-plus me-2"></i> Add New Item
                            </button>
                        </div>
                        <!-- Alerts -->
                        @if(($stats['low_stock'] ?? 0) > 0)
                        <div class="alert alert-warning border-danger-subtle mb-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-triangle-exclamation me-2 text-danger"></i>
                                <strong>{{ $stats['low_stock'] }} items</strong>
                                <span class="ms-2">are running low on stock and need restocking.</span>
                            </div>
                        </div>
                        @endif
                        @if(($stats['expiring_soon'] ?? 0) > 0)
                        <div class="alert alert-light border-warning mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2 text-warning"></i>
                                <strong>{{ $stats['expiring_soon'] }} items</strong>
                                <span class="ms-2">are expiring within 90 days.</span>
                            </div>
                        </div>
                        @endif

                        <!-- Stat Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="inventory-card py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">Total Items</div>
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $stats['total_items'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="inventory-card py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">Low Stock</div>
                                        <i class="fas fa-cubes-stacked text-warning"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $stats['low_stock'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="inventory-card py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">Out of Stock</div>
                                        <i class="fas fa-triangle-exclamation text-danger"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $stats['out_of_stock'] ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="inventory-card py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">Expiring Soon</div>
                                        <i class="fas fa-hourglass-half text-warning"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $stats['expiring_soon'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <form method="GET" class="d-flex align-items-end mb-3 gap-2">
                            <div class="flex-grow-1">
                                <label class="form-label mb-1">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by item name, ID, category, or location...">
                                </div>
                            </div>
                            <div style="min-width:220px;">
                                <label class="form-label mb-1">Category</label>
                                <select class="form-select" name="category" onchange="this.form.submit()">
                                    <option value="" @selected(!request('category'))>All Categories</option>
                                    @foreach(($categories ?? []) as $cat)
                                        <option value="{{ $cat }}" @selected(request('category')==$cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        @if($inventory->count() > 0)

                            <div class="table-responsive">
                                <table class="table table-hover align-middle bg-white rounded shadow-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Item Name</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col">Min Stock</th>
                                            <th scope="col">Expiry Date</th>
                                            <th scope="col">Location</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventory as $item)
                                        <tr>
                                            <td>
                                                <span class="stock-indicator @if($item->current_stock == 0) stock-out @elseif($item->current_stock <= $item->minimum_stock) stock-low @else stock-good @endif"></span>
                                            </td>
                                            <td>{{ $item->item_name }}</td>
                                            <td>{{ $item->category }}</td>
                                            <td><strong>{{ $item->current_stock }}</strong></td>
                                            <td>{{ $item->minimum_stock }}</td>
                                            <td>{{ $item->expiry_date ? \Illuminate\Support\Carbon::parse($item->expiry_date)->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $item->location ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $badge = 'secondary';
                                                    if ($item->status === 'in_stock') $badge = 'success';
                                                    elseif ($item->status === 'low_stock') $badge = 'warning';
                                                    elseif ($item->status === 'out_of_stock') $badge = 'danger';
                                                    elseif ($item->status === 'expired') $badge = 'dark';
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">{{ str_replace('_',' ', ucfirst($item->status)) }}</span>
                                            </td>
                                            <td class="d-flex gap-2">
                                                <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewDetailModal{{ $item->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#restockModal{{ $item->id }}">
                                                    <i class="fas fa-plus me-1"></i> Restock
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deductModal{{ $item->id }}">
                                                    <i class="fas fa-minus me-1"></i> Deduct
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Per-item View Modals -->
                            @foreach($inventory as $item)
                            <div class="modal fade" id="viewDetailModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Inventory Item Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="inventory-card">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="flex-grow-1">
                                                        <h6 class="fw-bold mb-1">{{ $item->item_name }}</h6>
                                                        <small class="text-muted">{{ $item->category }}</small>
                                                    </div>
                                                    <span class="stock-indicator @if($item->current_stock == 0) stock-out @elseif($item->current_stock <= $item->minimum_stock) stock-low @else stock-good @endif"></span>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Current Stock:</span>
                                                        <span class="fw-bold">{{ $item->current_stock }} {{ $item->unit }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">Minimum Stock:</span>
                                                        <span>{{ $item->minimum_stock }} {{ $item->unit }}</span>
                                                    </div>
                                                    @if($item->description)
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ $item->description }}</small>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#restockModal{{ $item->id }}">
                                                        <i class="fas fa-plus me-1"></i> Restock
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deductModal{{ $item->id }}">
                                                        <i class="fas fa-minus me-1"></i> Deduct
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Restock Modal -->
                            <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Restock: {{ $item->item_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.inventory.restock', $item) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Restock</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Deduct Modal -->
                            <div class="modal fade" id="deductModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Deduct: {{ $item->item_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.inventory.deduct', $item) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" min="1" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Deduct</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-box me-2 fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No inventory items found</h5>
                                <p class="text-muted">Start by adding your first inventory item.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="fas fa-plus me-2"></i> Add First Item
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.inventory.add') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="item_name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control" id="item_name" name="item_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Medicines">Medicines</option>
                                <option value="Medical Supplies">Medical Supplies</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Vaccines">Vaccines</option>
                                <option value="PPE">PPE</option>
                                <option value="Syringes & Needles">Syringes & Needles</option>
                                <option value="Lab Supplies">Lab Supplies</option>
                                <option value="Test Kits">Test Kits</option>
                                <option value="Disinfectants">Disinfectants</option>
                                <option value="Consumables">Consumables</option>
                                <option value="Dressings">Dressings</option>
                                <option value="Nutritional Supplements">Nutritional Supplements</option>
                                <option value="Oxygen Supplies">Oxygen Supplies</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_stock" class="form-label">Current Stock *</label>
                                    <input type="number" class="form-control" id="current_stock" name="current_stock" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="minimum_stock" class="form-label">Minimum Stock *</label>
                                    <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Cabinet A1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit *</label>
                            <select class="form-select" id="unit" name="unit" required>
                                <option value="">Select Unit</option>
                                <option value="pieces">Pieces</option>
                                <option value="boxes">Boxes</option>
                                <option value="bottles">Bottles</option>
                                <option value="vials">Vials</option>
                                <option value="units">Units</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme toggle persistence
        (function(){
            const key = 'app-theme';
            const btn = document.getElementById('themeToggle');
            if (btn) {
                btn.addEventListener('click', function(){
                    const isDark = document.body.classList.toggle('bg-dark');
                    document.body.classList.toggle('text-white', isDark);
                    localStorage.setItem(key, isDark ? 'dark' : 'light');
                });
            }
        })();
    </script>
</body>
</html>