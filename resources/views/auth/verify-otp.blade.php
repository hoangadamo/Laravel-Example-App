@extends('layout')
@section('content')
    <div class="container">
        <h2 class="mt-3 mb-3">Verify OTP</h2>
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
        <form method="POST" enctype="multipart/form-data" id="verify-form" action="{{ route('verifyOTP') }}">
            @csrf
            <div class="mb-3">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
        <div class="mt-3">
            <p>Didn't receive the OTP? <a href="{{ route('sendOTP') }}">Click here to resend</a></p>
        </div>
    </div>
@endsection
