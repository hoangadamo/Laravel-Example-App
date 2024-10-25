@extends('layout')
@section('content')
    <div class="container">
        <h2 class="mt-3 mb-3">Login</h2>
        <div>
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <form method="POST" enctype="multipart/form-data" id="login-form" action="{{ route('authenticate') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" />
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <div class="mt-3">
                <a href="{{ url('auth/google') }}"
                    style="margin-top: 0px !important;background: green;color: #ffffff;padding: 8px;border-radius:7px;" 
                    class="ml-2">
                    <strong>Google Login</strong>
                </a>
            </div>
            <div class="mt-3">
                <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                <p><a href="{{ route('enterEmail') }}">Forgot your password?</a></p>
            </div>
        </form>
    </div>
@endsection
