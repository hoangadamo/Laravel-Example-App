<!-- Update Modal -->
<div class="modal fade" id="updateBookModal" tabindex="-1" aria-labelledby="updateBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateBookModalLabel">Update Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" id="updateBookForm">
                    @csrf
                    @method('put')
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Title" id="title" />
                    </div>
                    <div class="mb-3">
                        <label for="publishedDate" class="form-label">Published Date</label>
                        <input type="date" name="publishedDate" class="form-control" placeholder="Published Date"
                            id="publishedDate" />
                    </div>
                    <div class="mb-3">
                        <label for="categories" class="form-label">Categories</label>
                        <select name="categoryIds[]" class="form-control" multiple>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
