<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="cart-add-url" content="{{ route('cart.add', ['id' => '__ID__']) }}">
    <meta name="search-price-range-url" content="{{ route('search.price.range') }}">

    <title>@yield('title', 'Shop Bán Hàng')</title>

    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/prettyPhoto.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/price-range.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/responsive.css') }}">
    @yield('css')
</head>
<body>

    @include('frontend.layouts.header')

    @yield('slide')

    <div class="container my-4">
        <div class="row">

            @section('menu_left')
                <div class="col-lg-3">
                    @includeIf('frontend.layouts.menu_left')
                </div>
            @show


            <div class="col-lg-9">
                @yield('content')
            </div>
        </div>
    </div>

    @include('frontend.layouts.footer')

    <script src="{{ asset('rate/js/jquery-1.9.1.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('frontend/js/price-range.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('frontend/js/jquery.prettyPhoto.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('frontend/js/product-detail.js') }}"></script>
    @yield('js')
</body>
</html>