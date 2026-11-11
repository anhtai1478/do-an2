@extends('frontend.layouts.master')

@section('title', $product->name . ' - Chi tiết sản phẩm')

@section('content')
    @php
        $images = array_slice($product->imagePaths(), 0, 5);
        $mainImage = !empty($images) ? asset('storage/' . $images[0]) : asset('frontend/images/product-details/1.jpg');
    @endphp

    <div class="product-details"><!--product-details-->
        <div class="col-sm-5">
            <div class="view-product">
                <a id="product-main-image-link" href="{{ $mainImage }}" aria-label="Phóng to ảnh sản phẩm">
                    <img id="product-main-image" src="{{ $mainImage }}" alt="{{ $product->name }}">
                    <h3>ZOOM</h3>
                </a>
            </div>

            @if (count($images) > 1)
                <div class="product-image-slider">
                    <button type="button" class="product-image-arrow" data-thumbnail-prev aria-label="Ảnh trước">
                        <i class="fa fa-angle-left"></i>
                    </button>
                    <div id="product-thumbnails-viewport">
                        <div id="product-thumbnails">
                            @foreach ($images as $image)
                                <button type="button" class="product-thumbnail {{ $loop->first ? 'active' : '' }}"
                                    data-index="{{ $loop->index }}"
                                    data-image="{{ asset('storage/' . $image) }}"
                                    aria-label="Xem ảnh {{ $loop->iteration }} của sản phẩm"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="product-image-arrow" data-thumbnail-next aria-label="Ảnh tiếp theo">
                        <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            @endif
        </div>

        <div class="col-sm-7">
            <div class="product-information"><!--/product-information-->
                @if ($product->is_on_sale)
                    <img src="{{ asset('frontend/images/home/sale.png') }}" class="newarrival" alt="Sale" />
                @else
                    <img src="{{ asset('frontend/images/product-details/new.jpg') }}" class="newarrival" alt="New Arrival" />
                @endif

                <h2>{{ $product->name }}</h2>
                <p>Web ID: 1088{{ $product->id }}</p>
                <img src="{{ asset('frontend/images/product-details/rating.png') }}" alt="Rating" style="display: block; margin: 8px 0 12px 0;" />

                <span>
                    @if ($product->discountedPrice())
                        <span style="font-size: 20px; text-decoration: line-through; color: #a6a6a6; margin-right: 12px; font-weight: normal; line-height: 33px;">
                            ${{ number_format((float) $product->price, 2) }}
                        </span>
                        <span>${{ $product->discountedPrice() }}</span>
                    @else
                        <span>US ${{ number_format((float) $product->price, 2) }}</span>
                    @endif

                    <label for="product-quantity">Số lượng:</label>
                    <input type="number" id="product-quantity" min="1" max="99" value="1">
                    <button type="button" class="btn btn-default cart add-to-cart"
                        data-add-to-cart
                        data-product-id="{{ $product->id }}"
                        data-cart-url="{{ route('cart.add', $product->id) }}">
                        <i class="fa fa-shopping-cart"></i>
                        Add to cart
                    </button>
                </span>

                <p><b>Tình trạng:</b> <span style="color: #4cae4c; font-weight: bold;">Còn hàng (In Stock)</span></p>
                <p><b>Trạng thái:</b> {{ $product->is_on_sale ? 'Đang giảm giá ' . ($product->discount_percent ? '(-' . $product->discount_percent . '%)' : '') : 'Hàng mới (New)' }}</p>
                <p><b>Danh mục:</b> {{ $product->category ?: 'Chưa cập nhật' }}</p>
                <p><b>Thương hiệu:</b> {{ $product->brand ?: 'Chưa cập nhật' }}</p>
                <a href="#"><img src="{{ asset('frontend/images/product-details/share.png') }}" class="share img-responsive" alt="Share" style="margin-top: 15px;" /></a>
            </div><!--/product-information-->
        </div>
    </div><!--/product-details-->

    <div id="product-image-modal" class="product-image-lightbox" aria-hidden="true">
        <button type="button" class="product-image-modal-close" data-close-product-image aria-label="Đóng ảnh">
            &times;
        </button>
        <img id="product-image-modal-image" src="{{ $mainImage }}" alt="{{ $product->name }}">
    </div>

    <div class="category-tab shop-details-tab">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab">Chi tiết sản phẩm</a></li>
                <li><a href="#company-profile" data-toggle="tab">Thông tin công ty</a></li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="details">
                <div class="col-sm-12" style="padding: 20px;">
                    <p style="font-size: 15px; line-height: 1.6; color: #555;">{{ $product->description ?: 'Chưa có mô tả chi tiết cho sản phẩm này.' }}</p>
                </div>
            </div>

            <div class="tab-pane fade" id="company-profile">
                <div class="col-sm-12" style="padding: 20px;">
                    <p style="font-size: 15px; line-height: 1.6; color: #555;">{{ $product->company_profile ?: 'Chưa có thông tin hồ sơ công ty.' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
