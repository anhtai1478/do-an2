@extends('frontend.layouts.master')

@section('title', 'Đặt lại mật khẩu mới - E-Shopper')

@section('content')
    <div class="login-form">
        <h2>Đặt lại mật khẩu mới</h2>
        <p style="color: #696763; margin-bottom: 20px;">
            Nhập email và mật khẩu mới để cập nhật lại mật khẩu cho tài khoản của bạn.
        </p>

        @if (session('status'))
            <div class="alert alert-success" style="background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px;">
                <i class="fa fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email" style="font-weight: 600; color: #363432;">Địa chỉ Email:</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" placeholder="Email Address" required autocomplete="email" autofocus>
            @error('email')
                <div class="text-danger" style="margin-bottom: 15px; margin-top: -10px;">
                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror

            <label for="password" style="font-weight: 600; color: #363432;">Mật khẩu mới:</label>
            <input id="password" type="password" name="password" placeholder="Mật khẩu mới (tối thiểu 8 ký tự)" required autocomplete="new-password">
            @error('password')
                <div class="text-danger" style="margin-bottom: 15px; margin-top: -10px;">
                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror

            <label for="password-confirm" style="font-weight: 600; color: #363432;">Xác nhận mật khẩu mới:</label>
            <input id="password-confirm" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" required autocomplete="new-password">

            <button type="submit" class="btn btn-default" style="margin-top: 15px;">
                <i class="fa fa-check"></i> Cập nhật mật khẩu mới
            </button>
        </form>

        <p style="margin-top: 25px;">
            <a href="{{ route('login') }}" style="color: #FE980F;">
                <i class="fa fa-arrow-left"></i> Quay lại Đăng nhập
            </a>
        </p>
    </div>
@endsection
