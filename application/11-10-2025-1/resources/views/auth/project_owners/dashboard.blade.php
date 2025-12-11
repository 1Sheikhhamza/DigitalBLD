@extends('auth.project_owners.layouts.app')
@section('seodetails')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@endsection
@section('title', "Welcome to Digital Bangladesh Legal Decisions")

@section('content')


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscriber Dashboard | GrowUp Agrotech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #333;
        }
        .sidebar .nav-link.active {
            background-color: #e8f0fe;
            font-weight: 500;
        }
        .card-box {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 150px;
        }
        .profile-warning {
            background-color: #ffe3e3;
            border: 1px solid #ffcccc;
            padding: 10px;
            border-radius: 4px;
            color: #d93025;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky">
                <h4 class="px-3 mb-3">GrowUp Agrotech</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-house-door me-2"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" data-bs-toggle="collapse" href="#productsMenu" role="button" aria-expanded="false" aria-controls="productsMenu">
                            <i class="bi bi-lightning me-2"></i> Products
                        </a>
                        <div class="collapse ps-4" id="productsMenu">
                            <a class="nav-link" href="#">All products</a>
                            <a class="nav-link" href="#">My Cart</a>
                            <a class="nav-link" href="#">My Orders</a>
                        </div>
                    </li>
                    <li>
                        <a class="nav-link" data-bs-toggle="collapse" href="#propertiesMenu" role="button" aria-expanded="false" aria-controls="propertiesMenu">
                            <i class="bi bi-cpu me-2"></i> Properties
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#"><i class="bi bi-layers me-2"></i> Grow Up</a>
                    </li>
                    <li>
                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#walletMenu" role="button" aria-expanded="false" aria-controls="walletMenu">
                                <span><i class="bi bi-wallet2 me-2"></i> Wallet</span>
                                <i class="bi bi-chevron-down small"></i>
                            </a>
                            <div class="collapse ps-4" id="walletMenu">
                                <a class="nav-link" href="#">Deposit</a>
                                <a class="nav-link" href="#">Withdraw</a>
                            </div>
                        </li>
                    <li>
                        <a class="nav-link" data-bs-toggle="collapse" href="#invoicesMenu" role="button" aria-expanded="false" aria-controls="invoicesMenu">
                            <i class="bi bi-receipt me-2"></i> Invoices
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#"><i class="bi bi-person me-2"></i> Profile</a>
                    </li>
                    <li>
                        <a class="nav-link" href="#"><i class="bi bi-people me-2"></i> Nominee</a>
                    </li>
                    <li>
                        <a class="nav-link" href="#"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="py-3">
                <div class="profile-warning mb-3">
                    <strong>Warning!</strong> Please provide your nominee info by <a href="#">Click here</a>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>Wallet Balance</h6>
                            <h4 class="text-orange">0/=</h4>
                            <a href="#">Deposit <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>Ordered properties</h6>
                            <h4>0</h4>
                            <a href="#">View all <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>My grow up projects</h6>
                            <h4>0</h4>
                            <a href="#">View all <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>Total investment</h6>
                            <h4>0/=</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>Today income</h6>
                            <h4>0/=</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>Total income</h6>
                            <h4>0/=</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-box">
                            <h6>My Profile</h6>
                            <h5 class="text-orange">Profile ID: 4724</h5>
                            <a href="#">View / Edit <i class="bi bi-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


@endsection