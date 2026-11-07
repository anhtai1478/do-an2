@extends('frontend.layouts.master')

@section('title', 'Cart | E-Shopper')

@section('menu_left')
@endsection

@section('content')
    <section id="cart_items" data-update-url="{{ url('/cart/update-ajax') }}">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Shopping Cart</li>
            </ol>
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
                        <td></td>
                    </tr>
                </thead>
                <tbody id="cart-body">
                    @forelse ($cart as $item)
                        <tr id="cart-row-{{ $item['id'] }}" data-id="{{ $item['id'] }}">
                            <td class="cart_product">
                                <a href="{{ route('frontend.products.show', $item['id']) }}">
                                    @if (!empty($item['image']))
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                            style="width: 110px; height: 110px; object-fit: cover;">
                                    @endif
                                </a>
                            </td>
                            <td class="cart_description">
                                <h4><a href="{{ route('frontend.products.show', $item['id']) }}">{{ $item['name'] }}</a></h4>
                                <p>Web ID: {{ $item['id'] }}</p>
                            </td>
                            <td class="cart_price">
                                <p>${{ number_format((float) $item['price'], 2) }}</p>
                            </td>
                            <td class="cart_quantity">
                                <div class="cart_quantity_button">
                                    <a class="cart_quantity_up" href="#" data-action="up" data-id="{{ $item['id'] }}"> + </a>
                                    <input class="cart_quantity_input" type="text" name="quantity"
                                        value="{{ $item['quantity'] }}" autocomplete="off" size="2" readonly>
                                    <a class="cart_quantity_down" href="#" data-action="down" data-id="{{ $item['id'] }}"> -
                                    </a>
                                </div>
                            </td>
                            <td class="cart_total">
                                <p class="cart_total_price" id="item-total-{{ $item['id'] }}">
                                    ${{ number_format((float) $item['price'] * (int) $item['quantity'], 2) }}
                                </p>
                            </td>
                            <td class="cart_delete">
                                <a class="cart_quantity_delete" href="#" data-action="delete" data-id="{{ $item['id'] }}">
                                    <i class="fa fa-times"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="cart-empty-row">
                            <td colspan="6" class="text-center" style="padding: 30px;">
                                Giỏ hàng của bạn đang trống.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="do_action">
        <div class="heading">
            <h3>What would you like to do next?</h3>
            <p>Choose if you have a discount code or reward points you want to use or would like to estimate your delivery
                cost.</p>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="chose_area">
                    <ul class="user_option">
                        <li>
                            <input type="checkbox">
                            <label>Use Coupon Code</label>
                        </li>
                        <li>
                            <input type="checkbox">
                            <label>Use Gift Voucher</label>
                        </li>
                        <li>
                            <input type="checkbox">
                            <label>Estimate Shipping & Taxes</label>
                        </li>
                    </ul>
                    <ul class="user_info">
                        <li class="single_field">
                            <label>Country:</label>
                            <select>
                                <option>United States</option>
                                <option>UK</option>
                                <option>Canada</option>
                            </select>
                        </li>
                        <li class="single_field">
                            <label>Region / State:</label>
                            <select>
                                <option>Select</option>
                                <option>London</option>
                                <option>Alaska</option>
                            </select>
                        </li>
                        <li class="single_field zip-field">
                            <label>Zip Code:</label>
                            <input type="text">
                        </li>
                    </ul>
                    <a class="btn btn-default update" href="#">Get Quotes</a>
                    <a class="btn btn-default check_out" href="{{ url('/') }}">Continue</a>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="total_area">
                    <ul>
                        <li>Cart Sub Total <span id="cart-sub-total">${{ number_format($subTotal, 2) }}</span></li>
                        <li>Eco Tax <span id="cart-eco-tax">${{ number_format($ecoTax, 2) }}</span></li>
                        <li>Shipping Cost <span>Free</span></li>
                        <li>Total <span id="cart-grand-total">${{ number_format($grandTotal, 2) }}</span></li>
                    </ul>
                    <a class="btn btn-default update" href="{{ route('cart.index') }}">Update</a>
                    <a class="btn btn-default check_out" href="{{ url('/checkout') }}">Check Out</a>
                </div>
            </div>
        </div>
    </section>
@endsection