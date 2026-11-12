@extends('admin.layouts.master')

@section('content')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Chỉnh sửa thông tin thành viên</h4>
        </div>
        <div class="col-7 align-self-center">
            <div class="d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit #{{ $user->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="mdi mdi-alert-circle"></i> Đã xảy ra lỗi:</strong>
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
            <h4 class="card-title">Chỉnh sửa thành viên: {{ $user->name }}</h4>
            <h5 class="card-subtitle mb-4">Cập nhật quyền hạn (Level) và các thông tin cá nhân của người dùng</h5>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                @method('PUT')

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Ảnh đại diện hiện tại:</label>
                    <div class="col-sm-9">
                        @php
                            $avatarSrc = asset('admin/assets/images/users/5.jpg');
                            if ($user->avatar) {
                                $avatarSrc = str_contains($user->avatar, '/')
                                    ? asset('storage/' . $user->avatar)
                                    : asset('upload/user/avatar/' . $user->avatar);
                            }
                        @endphp
                        <img src="{{ $avatarSrc }}" alt="{{ $user->name }}" class="rounded-circle mb-2" width="80" height="80" style="object-fit: cover;">
                        <input type="file" name="avatar" class="form-control-file">
                        <small class="form-text text-muted">Chọn file ảnh mới nếu muốn thay đổi avatar (JPEG, PNG, JPG, GIF tối đa 2MB).</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Phân quyền (Level) <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <select name="level" class="form-control" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="0" {{ old('level', (string)$user->level) === '0' ? 'selected' : '' }}>Member (Khách hàng / Thành viên thông thường - Level 0)</option>
                            <option value="1" {{ old('level', (string)$user->level) === '1' ? 'selected' : '' }}>Staff (Nhân viên - Level 1)</option>
                            <option value="2" {{ old('level', (string)$user->level) === '2' ? 'selected' : '' }}>Admin (Quản trị viên toàn quyền - Level 2)</option>
                        </select>
                        @if ($user->id === auth()->id())
                            <input type="hidden" name="level" value="2">
                            <small class="form-text text-info">Bạn không thể thay đổi cấp bậc của chính mình.</small>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Họ và tên <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Địa chỉ Email <span class="text-danger">*</span>:</label>
                    <div class="col-sm-9">
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Số điện thoại:</label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Địa chỉ:</label>
                    <div class="col-sm-9">
                        <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Quốc gia:</label>
                    <div class="col-sm-9">
                        <select name="id_country" class="form-control">
                            <option value="">-- Chọn quốc gia --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ old('id_country', $user->id_country) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Mật khẩu mới:</label>
                    <div class="col-sm-9">
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không muốn đổi mật khẩu">
                        <small class="form-text text-muted">Chỉ nhập nếu cần đổi mật khẩu mới cho thành viên này (tối thiểu 6 ký tự).</small>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">
                            <i class="mdi mdi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

