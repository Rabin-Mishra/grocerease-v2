<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GrocerEase')</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Premium Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Outfit', sans-serif !important;
        }
        /* Premium Green Footer Styling */
        .premium-footer {
            background: linear-gradient(135deg, #198754 0%, #115c36 100%);
            border-top: 4px solid #ffda6a; /* Vibrant premium golden border */
            position: relative;
            overflow: hidden;
            font-size: 0.95rem;
        }
        .premium-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 218, 106, 0.4), transparent);
        }
        .footer-brand-title {
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            color: #ffffff !important;
            font-weight: 700;
        }
        .dev-badge {
            background: #ffffff !important;
            color: #115c36 !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        .dev-badge:hover {
            transform: translateY(-2px);
            background: #ffda6a !important;
            color: #115c36 !important;
        }
        .nec-badge {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: #ffffff !important;
            font-size: 0.8rem;
        }
        .contact-item {
            transition: all 0.3s ease;
            color: #e8f5e9 !important; /* extremely clear high contrast light-green */
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .contact-item i {
            color: #ffda6a !important; /* highly visible premium golden yellow */
            transition: all 0.3s ease;
            width: 24px;
        }
        .contact-item:hover {
            color: #ffffff !important;
            transform: translateX(5px);
        }
        .contact-item:hover i {
            color: #ffffff !important;
            transform: scale(1.1);
        }
        .footer-link {
            color: #e8f5e9 !important; /* very readable light-green text */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            text-decoration: none;
        }
        .footer-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #ffda6a; /* golden yellow underline */
            transition: width 0.3s ease;
        }
        .footer-link:hover {
            color: #ffffff !important;
        }
        .footer-link:hover::after {
            width: 100%;
        }
        .social-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff !important;
            transition: all 0.3s ease;
        }
        .social-btn:hover {
            background: #ffda6a !important;
            color: #115c36 !important;
            transform: translateY(-3px) rotate(8deg);
            box-shadow: 0 4px 12px rgba(255, 218, 106, 0.3);
        }
        .footer-divider {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg bg-success navbar-dark py-3">
        <div class="container-fluid px-lg-5 px-md-4 px-3">
            <a class="navbar-brand fs-4" href="{{ url('/') }}">
                Grocer<span class="fw-bold">Ease</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/products') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#categories-section">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}#brands-section">Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact-footer">Contact</a>
                    </li>
                </ul>
                <form class="d-flex me-3" action="{{ url('/search') }}" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search groceries..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
                <div class="d-flex align-items-center">
                    <a href="{{ url('/cart') }}" class="text-white me-3 position-relative text-decoration-none">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    
                    @guest
                        <a href="{{ url('/login') }}" class="btn btn-outline-light btn-sm px-3">Login</a>
                    @else
                        <div class="dropdown">
                            <a class="text-white text-decoration-none dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user-circle me-1"></i> {{ $authUser->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                @if($authUser->isAdmin())
                                    <li><a class="dropdown-item fw-bold text-success" href="{{ url('/admin/dashboard') }}"><i class="fa-solid fa-gauge me-1"></i>Admin Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ url('/orders') }}"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-lg-5 px-md-4 px-3 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <main class="py-4 flex-grow-1">
        @yield('content')
    </main>

    <footer id="contact-footer" class="premium-footer text-white py-5 mt-auto">
        <div class="container-fluid px-lg-5 px-md-4 px-3">
            <div class="row g-4">
                <!-- Brand Column -->
                <div class="col-lg-4 col-md-6">
                    <h4 class="footer-brand-title mb-3">
                        <i class="fa-solid fa-leaf text-warning me-2"></i>Grocer<span class="fw-bold text-white">Ease</span>
                    </h4>
                    <p class="text-white mb-4" style="line-height: 1.6; opacity: 0.95;">
                        Your neighborhood grocery assistant. Fresh organic items sourced directly from local Nepalese farms and delivered straight to your doorstep.
                    </p>
                    <div class="d-flex align-items-center gap-2 text-white-50">
                        <span class="text-white" style="opacity: 0.85;">Made with ❤️ in Nepal</span>
                        <span class="fs-5">🇳🇵</span>
                    </div>
                </div>

                <!-- Navigation Column -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white fw-semibold mb-3">Quick Navigation</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="{{ url('/') }}" class="footer-link">Home Portal</a></li>
                        <li><a href="{{ url('/products') }}" class="footer-link">Product Catalog</a></li>
                        <li><a href="{{ url('/cart') }}" class="footer-link">Shopping Cart</a></li>
                        @guest
                            <li><a href="{{ url('/login') }}" class="footer-link">Join Storefront</a></li>
                        @else
                            <li><a href="{{ url('/orders') }}" class="footer-link">My Order History</a></li>
                            @if($authUser->isAdmin())
                                <li><a href="{{ url('/admin/dashboard') }}" class="footer-link"><i class="fa-solid fa-lock me-1 text-warning"></i>Admin Terminal</a></li>
                            @endif
                        @endguest
                    </ul>
                </div>

                <!-- Developer Column -->
                <div class="col-lg-5 col-md-12">
                    <div class="p-4 rounded-4 dev-card" style="background: rgba(0, 0, 0, 0.22); border: 1px solid rgba(255, 255, 255, 0.3);">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="text-white fw-bold mb-0" style="text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);">Rabin Mishra</h5>
                                <span class="badge dev-badge py-1 px-2.5 mt-1 small">IT Engineer</span>
                            </div>
                            <span class="badge nec-badge rounded-pill py-1.5 px-3">NEC Regd: 97236</span>
                        </div>
                        <p class="text-white small mb-3" style="opacity: 0.95;">Designed, architected, and engineered the core micro-services for GrocerEase.</p>
                        
                        <div class="d-flex flex-column gap-2.5">
                            <a href="mailto:info@rabinmishra.com.np" class="contact-item small">
                                <i class="fa-solid fa-envelope"></i>info@rabinmishra.com.np
                            </a>
                            <a href="https://rabinmishra.com.np" target="_blank" class="contact-item small">
                                <i class="fa-solid fa-globe"></i>rabinmishra.com.np
                            </a>
                            <a href="tel:+9779824059780" class="contact-item small">
                                <i class="fa-solid fa-phone"></i>+977 9824059780
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider my-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-white small" style="opacity: 0.85;">
                    &copy; {{ date('Y') }} GrocerEase. All Rights Reserved.
                </div>
                <!-- Social Connections -->
                <div class="d-flex gap-3">
                    <a href="mailto:info@rabinmishra.com.np" class="social-btn" title="Email Developer">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                    <a href="https://rabinmishra.com.np" target="_blank" class="social-btn" title="Developer Portfolio">
                        <i class="fa-solid fa-globe"></i>
                    </a>
                    <a href="tel:+9779824059780" class="social-btn" title="Direct Line">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
