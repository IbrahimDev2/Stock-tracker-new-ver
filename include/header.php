<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/Stock-tracker-new-ver/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Main navigation bar for the Inventory Management System -->
    <nav class="navbar navbar-expand-lg">
        <div class="container px-4 d-flex flex-wrap align-items-center justify-content-between">
            <a class="navbar-brand me-4" href="/Stock-tracker-new-ver/dashboard.php">
                <i class="fas fa-boxes"></i> Inventory System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/Stock-tracker-new-ver/dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-box"></i> Products
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/products/index.php">View All</a></li>
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/products/add.php">Add New</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/categories/index.php">View All</a></li>
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/categories/add.php">Add New</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="stockDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-warehouse"></i> Stock
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/stock/movements.php">Movements</a></li>
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/stock/add_movement.php">Add Movement</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/reports/index.php">All Products</a></li>
                            <li><a class="dropdown-item" href="/Stock-tracker-new-ver/reports/low_stock.php">Low Stock</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/Stock-tracker-new-ver/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
