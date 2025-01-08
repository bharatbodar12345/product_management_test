<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Products</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#productModal" id="addProductBtn">Add Product</button>
        <A class="btn btn-success mb-3" href="{{route('admin.logout')}}">Logout<a>

        <table class="table table-bordered" id="productsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <!-- Modal for Add/Edit Product -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                    </div>
                    <div class="modal-body">
                        <form id="productForm">
                            @csrf
                            <input type="hidden" id="productId">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="productName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price</label>
                                <input type="text" class="form-control" id="productPrice" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="productStock" class="form-label">Stock</label>
                                <input type="text" class="form-control" id="productStock" name="stock" required>
                            </div>
                            {{-- <div class="mb-3">
                                <label for="productCategory" class="form-label">Category</label>
                                <select class="form-select" id="productCategory" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <button type="submit" class="btn btn-primary" id="saveProductBtn">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            // Initialize DataTable with AJAX
            var table = $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.products.data') }}',
                columns: [
                    { name:'name', data: 'name' },
                    { name: 'price', data: 'price' },
                    { name:'stock', data: 'stock' },
                    { name:'action',data: 'action', orderable: false, searchable: false }
                ]
            });

            // Open Add Product modal
            $('#addProductBtn').click(function() {
                $('#productForm')[0].reset();
                $('#productModalLabel').text('Add Product');
                $('#productId').val('');
                $('#saveProductBtn').text('Save');
            });

            // Save product (Add/Edit)
            $('#productForm').submit(function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                let productId = $('#productId').val();
                var url = "{{ route('admin.products.update', ['product' => '_id']) }}";
                    url = url.replace('_id', productId);

                if (productId) {
                    // Update product
                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: formData,
                        success: function(response) {
                            $('#productModal').modal('hide');
                            table.ajax.reload();
                        }
                    });
                } else {
                    // Add new product
                    $.ajax({
                        url: '{{ route('admin.products.store') }}',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#productModal').modal('hide');
                            table.ajax.reload();
                        }
                    });
                }
            });

            // Edit product
            $(document).on('click', '.edit-btn', function() {
                let productId = $(this).data('id');
                // Generate the URL directly using the productId
                var url = "{{ route('admin.products.getbyid', ['id' => '__productId__']) }}".replace('__productId__', productId);

                // Use the URL in the $.get request
                $.get(url, function(product) {

                    $('#productId').val(product.id);
                    $('#productName').val(product.name);
                    $('#productPrice').val(product.price);
                    $('#productStock').val(product.stock);
                    $('#productCategory').val(product.category_id);
                    $('#productModalLabel').text('Edit Product');
                    $('#saveProductBtn').text('Update');
                    $('#productModal').modal('show');
                });
            });

            // Delete product
            $(document).on('click', '.delete-btn', function() {
                let productId = $(this).data('id');
                var url = "{{ route('admin.products.destroy', ['product' => '__productId__']) }}";
                    url = url.replace('__productId__', productId);
                if (confirm('Are you sure you want to delete this product?')) {
                    $.ajax({
                        // url: '/admin/products/' + productId,
                        url:url,
                        data: {
                            _token: csrfToken // Pass CSRF token as part of the data
                        },
                        method: 'DELETE',
                        success: function(response) {
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
