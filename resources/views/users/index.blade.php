@extends('layout')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">User</h1>
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
        <!-- Button to trigger modal -->
        <div class="mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                Create a New User
            </button>
        </div>
        {{-- id="usersTable" --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Image</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            </tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><img src="{{ $user->imageUrl }}" style="width: 200px; height: auto;" /></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-id="{{ $user->id }}" id="edit-btn">
                            Edit
                        </button>
                    </td>
                    <td>
                        <form method="post" action="{{ route('user.destroy', ['id' => $user->id]) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{-- Modals --}}
        @include('users.create-user')
        @include('users.update-user')

    </div>
    <script>
        $(document).on('click', '#edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: `/user/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    const imageUrl = data.imageUrl;
                    $('#image').attr('src', imageUrl ? imageUrl : 'path/to/default/image.png');
                    $('#updateUserForm').attr('action', `/user/${data.id}/update`);
                    var updateUserModal = new bootstrap.Modal(document.getElementById(
                        'updateUserModal'));
                    updateUserModal.show();
                },
                error: function(error) {
                    console.error('Error fetching user data:', error);
                }
            });
        });

        $(document).ready(function() {
            $('#usersTable').DataTable({
                "paging": true,
                "lengthMenu": [3, 9, 12],
                "pageLength": 3,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        })

        function previewImage() {
            const input = document.getElementById('image');
            const preview = document.getElementById('imagePreview');

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
