@extends('frontend.layouts.master')

@section('title', 'Quên mật khẩu - E-Shopper')

@section('content')
    <div class="login-form">
        <h2>Quên mật khẩu</h2>
        <p style="color: #696763; margin-bottom: 20px;">
            Vui lòng nhập địa chỉ email đã đăng ký tài khoản. Chúng tôi sẽ gửi một liên kết để bạn đặt lại mật khẩu mới.
        </p>

        @if (session('status'))
            <div class="alert alert-success" style="background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px;">
                <i class="fa fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <input type="email" name="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ Email của bạn" required autocomplete="email" autofocus>

            @error('email')
                <div class="text-danger" style="margin-bottom: 15px; margin-top: -10px;">
                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror

            <button type="submit" class="btn btn-default" style="margin-top: 10px;">
                <i class="fa fa-envelope"></i> Gửi link đặt lại mật khẩu
            </button>
        </form>

        <p style="margin-top: 25px;">
            <a href="{{ route('login') }}" style="color: #FE980F;">
                <i class="fa fa-arrow-left"></i> Quay lại Đăng nhập
            </a>
        </p>
    </div>
@endsection
