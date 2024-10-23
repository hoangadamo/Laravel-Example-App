@extends('layout')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4">Dashboard</h1>
                <p class="lead">Welcome to your dashboard.</p>
                <a href="{{ route('user.index') }}" class="btn btn-primary btn-lg mt-4">Go to User Index</a>
                <a href="{{ route('category.index') }}" class="btn btn-primary btn-lg mt-4">Go to Category Index</a>
            </div>
        </div>
    </div>
@endsection
