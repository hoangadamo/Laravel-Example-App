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
        {{-- id="user-table" --}}
        <table class="table table-bordered" id="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Image</th>
                    <th>Status</th>
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
                    <td>{{ $user->age }}</td>
                    <td><img src="{{ $user->imageUrl }}" style="width: 200px; height: auto;" /></td>
                    <td>
                        <form method="post" action="{{ route('user.toggleStatus', ['id' => $user->id]) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm"
                                style="background-color: {{ $user->isActive ? 'red' : 'green' }};">
                                {{ $user->isActive ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#updateUserModal" data-id="{{ $user->id }}" id="edit-btn">
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
                url: '{{ route('user.edit', ['id' => 'ID']) }}'.replace('ID', id),
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#age').val(data.age);
                    const imageUrl = data.imageUrl;
                    $('#updateImage').attr('src', imageUrl ? imageUrl : 'path/to/default/image.png');
                    $('#updateUserForm').attr('action', `/admin/user/${data.id}/update`);
                    var updateUserModal = bootstrap.Modal.getOrCreateInstance(document.getElementById(
                        'updateUserModal'));
                    updateUserModal.show();
                },
                error: function(error) {
                    console.error('Error fetching user data:', error);
                }
            });
        });

        $(document).ready(function() {
            $('#user-table').DataTable({
                "paging": true,
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        })

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
