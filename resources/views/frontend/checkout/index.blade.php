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
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 80px; height: 80px; object-fit: cover;">
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
                            <input class="cart_quantity_input" type="text" value="{{ $item['quantity'] }}" size="2" readonly>
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

