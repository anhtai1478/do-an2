@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Quản lý User</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-md-flex align-items-center mb-3">
                <div>
                    <h4 class="card-title">Danh sách tất cả thành viên</h4>
                    <h5 class="card-subtitle">Hiển thị thông tin người dùng và phân quyền (Admin / Member)</h5>
                </div>
            </div>

            <!-- Form Tìm kiếm & Lọc -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm theo tên, email, số điện thoại..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="level" class="form-control">
                            <option value="">-- Tất cả cấp bậc (Level) --</option>
                            <option value="2" {{ request('level') === '2' ? 'selected' : '' }}>Admin (Cấp 2)</option>
                            <option value="1" {{ request('level') === '1' ? 'selected' : '' }}>Staff (Cấp 1)</option>
                            <option value="0" {{ request('level') === '0' ? 'selected' : '' }}>Member (Cấp 0)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i> Tìm kiếm
                        </button>
                        @if (request()->hasAny(['search', 'level']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
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
                            <th class="border-top-0">Avatar</th>
                            <th class="border-top-0">Họ & Tên</th>
                            <th class="border-top-0">Email</th>
                            <th class="border-top-0">Điện thoại</th>
                            <th class="border-top-0">Quốc gia</th>
                            <th class="border-top-0">Cấp bậc (Level)</th>
                            <th class="border-top-0">Ngày tạo</th>
                            <th class="border-top-0 text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td><strong>#{{ $user->id }}</strong></td>
                                <td>
                                    @php
                                        $avatarSrc = asset('admin/assets/images/users/5.jpg');
                                        if ($user->avatar) {
                                            $avatarSrc = str_contains($user->avatar, '/')
                                                ? asset('storage/' . $user->avatar)
                                                : asset('upload/user/avatar/' . $user->avatar);
                                        }
                                    @endphp
                                    <img src="{{ $avatarSrc }}" alt="{{ $user->name }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if ($user->id === auth()->id())
                                        <span class="badge badge-pill badge-info ml-1">Bạn</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: '---' }}</td>
                                <td>{{ $user->country?->name ?: '---' }}</td>
                                <td>
                                    @if ((int) $user->level === 2)
                                        <span class="badge badge-danger p-2" style="font-size: 12px;">
                                            <i class="mdi mdi-shield-account"></i> Admin
                                        </span>
                                    @elseif ((int) $user->level === 1)
                                        <span class="badge badge-warning p-2" style="font-size: 12px; color: #fff;">
                                            <i class="mdi mdi-account-check"></i> Staff
                                        </span>
                                    @else
                                        <span class="badge badge-success p-2" style="font-size: 12px;">
                                            <i class="mdi mdi-account"></i> Member
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '---' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Chỉnh sửa thông tin">
                                        <i class="mdi mdi-pencil"></i> Sửa
                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa thành viên {{ $user->name }} không? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa thành viên">
                                                <i class="mdi mdi-delete"></i> Xóa
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="Không thể tự xóa chính mình">
                                            <i class="mdi mdi-delete"></i> Xóa
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-account-off" style="font-size: 24px;"></i>
                                    <p class="mt-2 mb-0">Không tìm thấy thành viên nào phù hợp.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

