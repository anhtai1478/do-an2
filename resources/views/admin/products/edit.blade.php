@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Chỉnh sửa sản phẩm</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit #{{ $product->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="mdi mdi-alert-circle"></i> Vui lòng kiểm tra lại dữ liệu:</strong>
            <ul class="mb-0 mt-1 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title">Sửa sản phẩm: {{ $product->name }}</h4>
                    <h5 class="card-subtitle">
                        Người đăng: <strong>{{ $product->user?->name ?? 'Không rõ' }}</strong> ({{ $product->user?->email ?? '---' }})
                    </h5>
                </div>
                <a href="{{ route('frontend.products.show', $product->id) }}" target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="mdi mdi-eye"></i> Xem sản phẩm ngoài Web
                </a>
            </div>

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                @method('PUT')

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Tên sản phẩm <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Giá bán ($) <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Danh mục (Category) <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="text" name="category" list="product-categories" class="form-control" value="{{ old('category', $product->category) }}" required>
                        <datalist id="product-categories">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Thương hiệu (Brand) <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="text" name="brand" list="product-brands" class="form-control" value="{{ old('brand', $product->brand) }}" required>
                        <datalist id="product-brands">
                            @foreach ($brands as $br)
                                <option value="{{ $br }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Trạng thái Sale:</label>
                    <div class="col-sm-4">
                        <select name="is_on_sale" id="is_on_sale" class="form-control">
                            <option value="0" {{ old('is_on_sale', (string)$product->is_on_sale) === '0' ? 'selected' : '' }}>Không Sale (Bình thường)</option>
                            <option value="1" {{ old('is_on_sale', (string)$product->is_on_sale) === '1' ? 'selected' : '' }}>Đang Sale (Giảm giá)</option>
                        </select>
                    </div>
                    <div class="col-sm-5" id="discount_wrapper">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Giảm (%)</span>
                            </div>
                            <input type="number" name="discount_percent" class="form-control" min="0" max="100"
                                value="{{ old('discount_percent', $product->discount_percent ?? 0) }}" placeholder="% giảm">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Hồ sơ công ty (Company profile):</label>
                    <div class="col-sm-9">
                        <input type="text" name="company_profile" class="form-control" value="{{ old('company_profile', $product->company_profile) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Mô tả sản phẩm:</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <!-- Danh sách ảnh hiện tại -->
                @php
                    $images = $product->imagePaths();
                @endphp
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Hình ảnh hiện tại:</label>
                    <div class="col-sm-9">
                        @if (count($images) > 0)
                            <div class="row">
                                @foreach ($images as $img)
                                    <div class="col-3 col-md-2 mb-2 text-center">
                                        <div style="border: 1px solid #ddd; padding: 4px; border-radius: 4px; background: #fff;">
                                            <img src="{{ asset('storage/' . $img) }}" alt="product image" style="width: 100%; height: 70px; object-fit: cover;">
                                            <div class="mt-1">
                                                <label class="small text-danger mb-0" style="cursor: pointer;">
                                                    <input type="checkbox" name="delete_images[]" value="{{ $img }}"> Xóa ảnh
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">Sản phẩm chưa có hình ảnh nào.</p>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Tải thêm ảnh mới:</label>
                    <div class="col-sm-9">
                        <input type="file" name="images[]" multiple class="form-control-file">
                        <small class="form-text text-muted">Có thể chọn nhiều ảnh cùng lúc (tổng số ảnh giữ lại + ảnh mới tối đa 5 ảnh).</small>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ml-2">
                            <i class="mdi mdi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

