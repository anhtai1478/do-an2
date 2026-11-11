@extends('frontend.layouts.master')

@section('title', 'Search Advanced - E-Shopper')

@section('content')
    <div class="features_items">
        <h2 class="title text-center">Search Advanced</h2>

        {{-- Form tìm kiếm nâng cao (Gửi bằng GET để vừa lọc vừa giữ được phân trang) --}}
        <div class="search-advanced-form" style="margin-bottom: 25px;">
            <form action="{{ route('search.advanced') }}" method="GET">
                <div style="display: flex; gap: 6px; margin-bottom: 12px;">
                    {{-- 1. Ô Name --}}
                    <div style="flex: 1; min-width: 0;">
                        <input type="text" name="name" placeholder="Name" value="{{ request('name') }}"
                            style="width: 100%; height: 40px; background: #F0F0E9; border: none; border-radius: 0; box-shadow: none; padding: 6px 12px; color: #696763; font-size: 14px; outline: none;">
                    </div>

                    {{-- 2. Ô Choose price --}}
                    <div style="flex: 1; min-width: 0;">
                        <select name="price"
                            style="width: 100%; height: 40px; background: #F0F0E9; border: none; border-radius: 0; box-shadow: none; padding: 6px 12px; color: #696763; font-size: 14px; outline: none;">
                            <option value="">Choose price</option>
                            <option value="0-1000" {{ request('price') === '0-1000' ? 'selected' : '' }}>&lt; 1000</option>
                            <option value="1000-2000" {{ request('price') === '1000-2000' ? 'selected' : '' }}>1000 - 2000</option>
                            <option value="2000-5000" {{ request('price') === '2000-5000' ? 'selected' : '' }}>2000 - 5000</option>
                            <option value="5000-100000" {{ request('price') === '5000-100000' ? 'selected' : '' }}>&gt; 5000</option>
                        </select>
                    </div>

                    {{-- 3. Ô Category --}}
                    <div style="flex: 1; min-width: 0;">
                        <select name="category"
                            style="width: 100%; height: 40px; background: #F0F0E9; border: none; border-radius: 0; box-shadow: none; padding: 6px 12px; color: #696763; font-size: 14px; outline: none;">
                            <option value="">Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Ô Brand --}}
                    <div style="flex: 1; min-width: 0;">
                        <select name="brand"
                            style="width: 100%; height: 40px; background: #F0F0E9; border: none; border-radius: 0; box-shadow: none; padding: 6px 12px; color: #696763; font-size: 14px; outline: none;">
                            <option value="">Brand</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b }}" {{ request('brand') == $b ? 'selected' : '' }}>
                                    {{ $b }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 5. Ô Status --}}
                    <div style="flex: 1; min-width: 0;">
                        <select name="status"
                            style="width: 100%; height: 40px; background: #F0F0E9; border: none; border-radius: 0; box-shadow: none; padding: 6px 12px; color: #696763; font-size: 14px; outline: none;">
                            <option value="">Status</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>New</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Sale</option>
                        </select>
                    </div>
                </div>

                {{-- Nút Search --}}
                <div>
                    <button type="submit" class="btn btn-default"
                        style="background: #FE980F; color: #fff; border: none; border-radius: 0; padding: 7px 24px; font-size: 14px;">
                        Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Danh sách sản phẩm tìm được --}}
        <div class="row">
            @forelse ($products as $item)
                <div class="col-sm-4">
                    <div class="product-image-wrapper">
                        <div class="single-products">
                            <div class="productinfo text-center">
                                @if ($item->primaryImage())
                                    <a href="{{ route('frontend.products.show', $item) }}">
                                        <img src="{{ asset('storage/' . $item->primaryImage()) }}" alt="{{ $item->name }}">
                                    </a>
                                @endif

                                <h2>${{ number_format((float) $item->price, 2) }}</h2>
                                <p>{{ $item->name }}</p>
                                <button type="button" class="btn btn-default add-to-cart" data-add-to-cart
                                    data-product-id="{{ $item->id }}" data-cart-url="{{ route('cart.add', $item->id) }}">
                                    <i class="fa fa-shopping-cart"></i> Add to cart
                                </button>
                            </div>
                        </div>

                        <div class="choose">
                            <ul class="nav nav-pills nav-justified">
                                <li>
                                    <a href="{{ route('frontend.products.show', $item) }}">
                                        <i class="fa fa-plus-square"></i> Product Detail
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-sm-12">
                    <p class="text-center" style="padding: 30px;">Không tìm thấy sản phẩm nào khớp với điều kiện tìm kiếm.</p>
                </div>
            @endforelse
        </div>

        {{-- Phân trang sản phẩm --}}
        <div class="row text-center" style="margin-top: 20px;">
            <div class="col-sm-12">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection