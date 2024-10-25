@extends('layout')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Category</h1>
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                Create a New Category
            </button>
        </div>
        {{-- id="category-table" --}}
        <table class="table table-bordered" id="category-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            </tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#updateCategoryModal" data-id="{{ $category->id }}" id="edit-btn">
                            Edit
                        </button>
                    </td>
                    <td>
                        <form method="post" action="{{ route('category.destroy', ['id' => $category->id]) }}">
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
        @include('categories.create-category')
        @include('categories.update-category')

    </div>
    <script>
        $(document).on('click', '#edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: `/admin/category/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                    $('#updateCategoryForm').attr('action', `/admin/category/${data.id}/update`);
                    var updateCategoryModal = bootstrap.Modal.getOrCreateInstance(document
                        .getElementById(
                            'updateCategoryModal'));
                    updateCategoryModal.show();
                },
                error: function(error) {
                    console.error('Error fetching category data:', error);
                }
            });
        });

        $(document).ready(function() {
            $('#category-table').DataTable({
                "paging": true,
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        })
    </script>
@endsection
