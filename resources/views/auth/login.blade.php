@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="card mx-auto mt-5" style="max-width: 480px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-1">Welcome Back</h3>
            <p class="text-muted text-center mb-4">Log in to your GrocerEase account</p>

            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="identifier" class="form-label">Email or Username</label>
                    <input type="text" class="form-control @error('identifier') is-invalid @enderror" id="identifier" name="identifier" value="{{ old('identifier') }}" autofocus required>
                    @error('identifier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-success w-100">Log In</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="text-decoration-none">Don't have an account? Register here</a>
            </div>
        </div>
    </div>
</div>
@endsection
