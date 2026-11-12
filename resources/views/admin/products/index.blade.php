@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Quản lý Product</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-md-flex align-items-center mb-3">
                <div>
                    <h4 class="card-title">Danh sách tất cả sản phẩm</h4>
                    <h5 class="card-subtitle">Hiển thị sản phẩm do các thành viên đăng tải, tìm kiếm theo tên member</h5>
                </div>
            </div>

            <!-- Form Tìm kiếm theo tên Member & Tên sản phẩm -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="small text-muted font-weight-bold">TÌM THEO TÊN MEMBER (NGƯỜI BÁN):</label>
                        <div class="input-group">
                            <input type="text" name="member_name" class="form-control"
                                placeholder="Nhập tên member cần tìm..."
                                value="{{ request('member_name') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small text-muted font-weight-bold">TÌM THEO TÊN SẢN PHẨM:</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Nhập tên sản phẩm..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="mdi mdi-magnify"></i> Tìm kiếm
                        </button>
                        @if (request()->hasAny(['member_name', 'search']))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-refresh"></i> Đặt lại
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped v-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-top-0">ID</th>
                            <th class="border-top-0">Hình ảnh</th>
                            <th class="border-top-0">Tên sản phẩm</th>
                            <th class="border-top-0">Giá</th>
                            <th class="border-top-0">Thành viên đăng</th>
                            <th class="border-top-0">Danh mục / Hiệu</th>
                            <th class="border-top-0">Khuyến mãi</th>
                            <th class="border-top-0">Ngày tạo</th>
                            <th class="border-top-0 text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td><strong>#{{ $product->id }}</strong></td>
                                <td>
                                    @php
                                        $primaryImg = $product->primaryImage();
                                    @endphp
                                    @if ($primaryImg)
                                        <img src="{{ asset('storage/' . $primaryImg) }}" alt="{{ $product->name }}"
                                             width="60" height="60" style="object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                    @else
                                        <div style="width: 60px; height: 60px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 11px;">
                                            No image
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ route('frontend.products.show', $product->id) }}" target="_blank" class="text-info">
                                            <i class="mdi mdi-open-in-new"></i> Xem ngoài Web
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    @if ($product->is_on_sale && $product->discount_percent > 0)
                                        <span class="text-danger font-weight-bold">${{ number_format($product->discountedPrice(), 2) }}</span>
                                        <br>
                                        <small class="text-muted"><del>${{ number_format((float) $product->price, 2) }}</del></small>
                                    @else
                                        <span class="font-weight-bold text-dark">${{ number_format((float) $product->price, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->user)
                                        <strong>{{ $product->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->user->email }}</small>
                                        <br>
                                        @if ((int)$product->user->level === 2)
                                            <span class="badge badge-danger">Admin</span>
                                        @else
                                            <span class="badge badge-success">Member</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Tài khoản đã bị xóa</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light border">{{ $product->category ?: 'Chưa phân loại' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $product->brand }}</small>
                                </td>
                                <td>
                                    @if ($product->is_on_sale && $product->discount_percent > 0)
                                        <span class="badge badge-danger">Sale -{{ $product->discount_percent }}%</span>
                                    @else
                                        <span class="badge badge-secondary">Bình thường</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at ? $product->created_at->format('d/m/Y') : '---' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Sửa sản phẩm">
                                        <i class="mdi mdi-pencil"></i> Sửa
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm \'{{ $product->name }}\' của member {{ $product->user?->name ?? '' }} không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa sản phẩm">
                                            <i class="mdi mdi-delete"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-package-variant" style="font-size: 24px;"></i>
                                    <p class="mt-2 mb-0">Không tìm thấy sản phẩm nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

