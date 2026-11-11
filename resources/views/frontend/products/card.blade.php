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
                <button type="button" class="btn btn-default add-to-cart"
                    data-add-to-cart
                    data-product-id="{{ $item->id }}"
                    data-cart-url="{{ route('cart.add', $item->id) }}">
                    <i class="fa fa-shopping-cart"></i> Add to cart
                </button>
            </div>

            <div class="product-overlay">
                <div class="overlay-content">
                    <h2>${{ number_format((float) $item->price, 2) }}</h2>
                    <p>{{ $item->name }}</p>
                    <button type="button" class="btn btn-default add-to-cart"
                        data-add-to-cart
                        data-product-id="{{ $item->id }}"
                        data-cart-url="{{ route('cart.add', $item->id) }}">
                        <i class="fa fa-shopping-cart"></i> Add to cart
                    </button>
                </div>
            </div>
        </div>

        <div class="choose">
            <ul class="nav nav-pills nav-justified">
                <li>
                    <a href="#">
                        <i class="fa fa-plus-square"></i> Add to wishlist
                    </a>
                </li>
                <li>
                    <a href="{{ route('frontend.products.show', $item) }}">
                        <i class="fa fa-plus-square"></i> Product Detail
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

