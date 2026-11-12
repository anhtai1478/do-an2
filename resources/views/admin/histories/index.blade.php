@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Quản lý Lịch sử Mua hàng</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lịch sử mua hàng</li>
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
                    <h4 class="card-title">Lịch sử giao dịch mua hàng</h4>
                    <h5 class="card-subtitle">Hiển thị toàn bộ thông tin các đơn hàng đã đặt của các member</h5>
                </div>
            </div>

            <!-- Form Tìm kiếm -->
            <form method="GET" action="{{ route('admin.histories.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm theo tên khách, email, số điện thoại..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-magnify"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </div>
                    @if (request()->filled('search'))
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.histories.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-refresh"></i> Đặt lại
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped v-middle">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-top-0">ID Đơn</th>
                            <th class="border-top-0">Khách mua hàng</th>
                            <th class="border-top-0">Email</th>
                            <th class="border-top-0">Số điện thoại</th>
                            <th class="border-top-0">Tài khoản Member</th>
                            <th class="border-top-0">Tổng tiền thanh toán</th>
                            <th class="border-top-0">Ngày đặt hàng</th>
                            <th class="border-top-0 text-center" style="width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histories as $history)
                            <tr>
                                <td><strong>#{{ $history->id }}</strong></td>
                                <td>
                                    <strong>{{ $history->name }}</strong>
                                </td>
                                <td>{{ $history->email }}</td>
                                <td>{{ $history->phone ?: '---' }}</td>
                                <td>
                                    @if ($history->user)
                                        <a href="{{ route('admin.users.edit', $history->user->id) }}" class="text-info font-weight-bold">
                                            <i class="mdi mdi-account"></i> {{ $history->user->name }}
                                        </a>
                                        <br>
                                        <small class="text-muted">(ID #{{ $history->user->id }})</small>
                                    @else
                                        <span class="text-muted">ID #{{ $history->id_user }} (Đã xóa)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-success font-weight-bold" style="font-size: 15px;">
                                        ${{ number_format((float) $history->price, 2) }}
                                    </span>
                                </td>
                                <td>{{ $history->created_at ? $history->created_at->format('d/m/Y H:i:s') : '---' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.histories.destroy', $history->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi lịch sử mua hàng #{{ $history->id }} không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa lịch sử">
                                            <i class="mdi mdi-delete"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-cart-off" style="font-size: 24px;"></i>
                                    <p class="mt-2 mb-0">Chưa có thông tin mua hàng nào trong hệ thống.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

