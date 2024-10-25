@extends('layout')
@section('content')
    <div class="container">
        <h2 class="mt-3 mb-3">Register</h2>
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
        <form method="POST" enctype="multipart/form-data" id="register-form" action="{{ route('store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control" placeholder="Name" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" />
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="text" name="age" class="form-control" placeholder="Age" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" />
            </div>
            <div class="mb-3">
                <label for="registerImage" class="form-label">Profile Image</label>
                <img id="registerImagePreview" src="#" alt="Image Preview"
                    style="display: none; max-width: 200px; margin-top: 10px;" />
                <input type="file" name="image" class="img-thumbnail" id="registerImage"
                    onchange="previewImage('registerImage', 'registerImagePreview')" />
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <div class="mt-3">
                <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
            </div>
        </form>
    </div>
    <script>
        function previewImage(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
@endsection
