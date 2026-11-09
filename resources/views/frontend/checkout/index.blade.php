@extends('frontend.layouts.master')

@section('title', 'Checkout | E-Shopper')

@section('menu_left')
@endsection

@section('content')
    <section id="cart_items">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Check out</li>
            </ol>
        </div>
            {{-- Hiển thị thông báo khi đặt hàng thành công --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Hiển thị lỗi nếu giỏ hàng trống hoặc nhập thiếu thông tin đăng ký nhanh --}}
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="shopper-informations" style="margin-bottom: 30px;">
                <div class="row">
                    @auth
                        {{-- TRƯỜNG HỢP 1: User ĐÃ LOGIN -> Chỉ cần bấm nút Order --}}
                        <div class="col-sm-6">
                            <div class="shopper-info">
                                <p>Thông tin người mua hàng (Đã đăng nhập)</p>
                                <form method="POST" action="{{ route('checkout.order') }}">
                                    @csrf
                                    <input type="text" value="{{ auth()->user()->name }}" readonly>
                                    <input type="email" value="{{ auth()->user()->email }}" readonly>
                                    <input type="text" value="{{ auth()->user()->phone ?? '' }}" readonly
                                        placeholder="Số điện thoại">
                                    <button type="submit" class="btn btn-primary">Order</button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- TRƯỜNG HỢP 2: User CHƯA CÓ TÀI KHOẢN -> Cho đăng ký nhanh tại chỗ & Order luôn --}}
                        <div class="col-sm-6">
                            <div class="signup-form">
                                <h2>Đăng ký nhanh & Đặt hàng</h2>
                                <form method="POST" action="{{ route('checkout.order') }}">
                                    @csrf
                                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Họ và tên *" required>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Địa chỉ Email *"
                                        required>
                                    <input type="password" name="password" placeholder="Mật khẩu (tối thiểu 6 ký tự) *"
                                        required>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại *"
                                        required>
                                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Địa chỉ">
                                    <button type="submit" class="btn btn-default">Đăng ký & Order</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="review-payment">
                <h2>Review & Payment</h2>
            </div>

            <div class="table-responsive cart_info">
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">Item</td>
                            <td class="description"></td>
                            <td class="price">Price</td>
                            <td class="quantity">Quantity</td>
                            <td class="total">Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subTotal = 0; @endphp
                        @forelse ($cart as $item)
                            @php
                                $itemTotal = (float) $item['price'] * (int) $item['quantity'];
                                $subTotal += $itemTotal;
                            @endphp
                            <tr>
                                <td class="cart_product">
                                    @if (!empty($item['image']))
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                </td>
                                <td class="cart_description">
                                    <h4>{{ $item['name'] }}</h4>
                                    <p>Web ID: {{ $item['id'] }}</p>
                                </td>
                                <td class="cart_price">
                                    <p>${{ number_format((float) $item['price'], 2) }}</p>
                                </td>
                                <td class="cart_quantity">
                                    <input class="cart_quantity_input" type="text" value="{{ $item['quantity'] }}" size="2"
                                        readonly>
                                </td>
                                <td class="cart_total">
                                    <p class="cart_total_price">${{ number_format($itemTotal, 2) }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Chưa có sản phẩm nào để thanh toán.</td>
                            </tr>
                        @endforelse

                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td colspan="2">
                                @php $ecoTax = count($cart) > 0 ? 2 : 0; @endphp
                                <table class="table table-condensed total-result">
                                    <tr>
                                        <td>Cart Sub Total</td>
                                        <td>${{ number_format($subTotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Eco Tax</td>
                                        <td>${{ number_format($ecoTax, 2) }}</td>
                                    </tr>
                                    <tr class="shipping-cost">
                                        <td>Shipping Cost</td>
                                        <td>Free</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td><span>${{ number_format($subTotal + $ecoTax, 2) }}</span></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </section>
@endsection