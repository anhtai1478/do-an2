@extends('frontend.layouts.master')

@section('title', $title)

@section('menu_left')
    <div class="col-lg-3">
        @include('frontend.layouts.menu-account')
    </div>
@endsection

@section('content')
    <div class="blog-post-area">
        <h2 class="title text-center">{{ $product->exists ? 'Update product!' : 'Create product!' }}</h2>

        <div class="signup-form">
            <form
                action="{{ $product->exists ? route('account.products.update', $product) : route('account.products.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if ($product->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $product->name) }}"
                        required maxlength="255">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input id="price" name="price" type="number" class="form-control" min="0.01" step="0.01"
                        value="{{ old('price', $product->price) }}" required>
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input id="category" name="category" type="text" list="product-categories"
                        placeholder="Please choose category" value="{{ old('category', $product->category) }}"
                        maxlength="255">
                    <datalist id="product-categories">
                        @foreach ($categories as $category)
                            <option value="{{ $category }}">
                        @endforeach
                    </datalist>
                    @error('category')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input id="brand" name="brand" type="text" list="product-brands" placeholder="Please choose brand"
                        value="{{ old('brand', $product->brand) }}" maxlength="255">
                    <datalist id="product-brands">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand }}">
                        @endforeach
                    </datalist>
                    @error('brand')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_on_sale">Sale</label>
                    <select id="is_on_sale" name="is_on_sale">
                        <option value="0" @selected((string) old('is_on_sale', (int) ($product->is_on_sale ?? false)) === '0')>
                            No sale
                        </option>
                        <option value="1" @selected((string) old('is_on_sale', (int) ($product->is_on_sale ?? false)) === '1')>
                            On sale
                        </option>
                    </select>
                    @error('is_on_sale')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount_percent">Discount</label>
                    <div class="account-product-discount">
                        <input id="discount_percent" name="discount_percent" type="number" min="0" max="100" step="1"
                            value="{{ old('discount_percent', $product->discount_percent ?? 0) }}">
                        <span>%</span>
                    </div>
                    @error('discount_percent')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="company_profile">Company profile</label>
                    <input id="company_profile" name="company_profile" type="text" placeholder="Company profile"
                        value="{{ old('company_profile', $product->company_profile) }}" maxlength="255">
                    @error('company_profile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @if ($product->exists && $product->imagePaths() !== [])
                    <div class="form-group">
                        <label>Ảnh hiện tại</label>

                        <div>
                            @foreach ($product->imagePaths() as $imagePath)
                                <div style="display: inline-block; margin-right: 12px; vertical-align: top">
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->name }}" width="80"
                                        height="80" style="object-fit: cover; display: block">

                                    <label>
                                        <input type="checkbox" name="delete_images[]" value="{{ $imagePath }}"
                                            @checked(in_array($imagePath, old('delete_images', []), true))>
                                        Xóa ảnh này
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="images">{{ $product->exists ? 'Add images' : 'Images' }}</label>
                    <input id="images" name="images[]" type="file" class="form-control"
                        accept="image/jpeg,image/png,image/webp" multiple>
                    <small class="text-muted">
                        @if ($product->exists)
                            Ảnh mới sẽ được thêm vào ảnh cũ chưa chọn xóa. Tổng số ảnh tối đa là 5.
                        @else
                            Chọn tối đa 5 ảnh.
                        @endif
                        Mỗi ảnh tối đa 2 MB.
                    </small>
                    @error('images')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @foreach ($errors->get('images.*') as $messages)
                        @foreach ($messages as $message)
                            <div class="text-danger">{{ $message }}</div>
                        @endforeach
                    @endforeach
                </div>

                <div class="form-group">
                    <label for="description">Detail</label>
                    <textarea id="description" name="description" class="form-control" rows="5" placeholder="Detail"
                        maxlength="5000">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="account-product-form-actions">
                    <button type="submit" class="btn btn-default">{{ $product->exists ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('account.my-product') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection