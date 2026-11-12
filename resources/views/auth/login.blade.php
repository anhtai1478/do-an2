@extends('frontend.layouts.master')

@section('title', 'Đăng nhập - E-Shopper')

@section('content')
    <div class="login-form">
        <h2>Login to your account</h2>

        @if (session('status'))
            <div class="alert alert-success" style="background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px;">
                <i class="fa fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required
                autocomplete="email" autofocus>

            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">

            <span>
                <input type="checkbox" name="remember" class="checkbox">
                Keep me signed in
            </span>

            <button type="submit" class="btn btn-default">Login</button>

            @error('email')
                <div class="text-danger" style="margin-top: 10px;">{{ $message }}</div>
            @enderror
            @error('password')
                <div class="text-danger" style="margin-top: 10px;">{{ $message }}</div>
            @enderror
        </form>

        <p style="margin-top: 15px;">
            <a href="{{ route('password.request') }}" style="color: #FE980F;">
                <i class="fa fa-lock"></i> Quên mật khẩu?
            </a>
        </p>

        <p>Chưa có tài khoản?
            <a href="{{ route('register') }}" style="color: #FE980F;">Đăng ký</a>
        </p>
    </div>
@endsection
