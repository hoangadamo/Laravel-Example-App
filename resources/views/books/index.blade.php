@extends('layout')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Book</h1>
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBookModal">
                Create a New Book
            </button>
        </div>
        {{-- id="book-table" --}}
        <table class="table table-bordered" id="book-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Published Date</th>
                    <th>Category</th>
                    <th>User Email</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            </tbody>
            @foreach ($books as $book)
                <tr>
                    <td>{{ $book->id }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->publishedDate->format('Y-m-d') }}</td>
                    <td>
                        @foreach ($book->categories as $category)
                            {{ $category->name }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $book->user->email }}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#updateBookModal" data-id="{{ $book->id }}" id="edit-btn">
                            Edit
                        </button>
                    </td>
                    <td>
                        <form method="post" action="{{ route('book.destroy', ['id' => $book->id]) }}">
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
        @include('books.create-book')
        @include('books.update-book')

    </div>
    <script>
        $(document).on('click', '#edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: `/admin/book/${id}/edit`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('#title').val(data.title);
                    let publishedDate = new Date(data.publishedDate);
                    let formattedDate = publishedDate.toISOString().split('T')[0];
                    $('#publishedDate').val(formattedDate);
                    $('#updateBookForm').attr('action', `/admin/book/${data.id}/update`);
                    var updateBookModal = bootstrap.Modal.getOrCreateInstance(document
                        .getElementById(
                            'updateBookModal'));
                    updateBookModal.show();
                },
                error: function(error) {
                    console.error('Error fetching book data:', error);
                }
            });
        });

        $(document).ready(function() {
            $('#book-table').DataTable({
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
