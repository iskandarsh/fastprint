<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom Tailwind for DataTable */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            margin: 0 4px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #45a049;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
            color: #333;
        }
        /* Modal */
        .modal-content {
            width: 500px;
            padding: 1.5rem;
        }
        .modal-header {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }
        .modal-button {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center mb-8">Product Management</h1>
        <button id="addProductBtn" class="px-6 py-3 bg-blue-500 text-white rounded shadow-md hover:bg-blue-600">Add Product</button>

        <table id="productTable" class="table-auto w-full mt-8 border-collapse bg-white shadow-lg rounded-lg">
            <thead>
                <tr class="bg-gray-200 text-gray-600">
                    <th class="px-6 py-4 border-b">No</th>
                    <th class="px-6 py-4 border-b">Product Name</th>
                    <th class="px-6 py-4 border-b">Price</th>
                    <th class="px-6 py-4 border-b">Category</th>
                    <th class="px-6 py-4 border-b">Status</th>
                    <th class="px-6 py-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Product rows will be populated via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg w-1/3 modal-content">
            <div class="modal-header" id="modalTitle">Add Product</div>
            <form id="productForm">
                <input type="hidden" id="productId" name="id_produk">
                <div class="mb-4">
                    <label for="nama_produk" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" id="nama_produk" name="nama_produk" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="harga" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="text" id="harga" name="harga" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="kategori_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="kategori_id" name="kategori_id" class="w-full px-3 py-2 border rounded select2" style="width: 100%" required></select>
                </div>
                <div class="mb-4">
                    <label for="status_id" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status_id" name="status_id" class="w-full px-3 py-2 border rounded select2" style="width: 100%" required>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded modal-button">Save</button>
                    <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-500 text-white rounded modal-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            loadProducts();

            $('#harga').on('input', function() {
                var value = $(this).val().replace(/[^\d]/g, ''); // Remove non-numeric characters
                if (value) {
                    value = 'Rp ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // Add comma as thousand separator
                }
                $(this).val(value); // Set the formatted value back to the input field
            });
            
            // Add product button click
            $('#addProductBtn').click(function() {
                $('#modalTitle').text('Add Product');
                loadCategoriesAndStatuses();
                $('#productForm')[0].reset();
                $('#productModal').removeClass('hidden');
            });

            // Close modal
            $('#closeModalBtn').click(function() {
                $('#productModal').addClass('hidden');
            });

            // Add/edit product form submission
            $('#productForm').submit(function(e) {
                e.preventDefault();
                
                // Get the value of 'harga' field and remove the 'Rp' and commas
                var harga = $('#harga').val().replace(/[^0-9]/g, '');  // Remove all non-numeric characters (Rp and commas)
                
                // Replace the 'harga' value in the form
                $('#harga').val(harga);
                
                // Serialize form data
                var formData = $(this).serialize();
                var url = $('#productId').val() ? 'update_product' : 'add_product';
                
                var addButton = $('#addProductBtn');
                addButton.prop('disabled', true);
                addButton.text('Loading...');
                
                $.ajax({
                    url: '<?= base_url('ProductController/') ?>' + url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#productModal').addClass('hidden');
                        loadProducts();
                        Swal.fire('Success', 'Product saved successfully!', 'success');
                        
                        addButton.prop('disabled', false);
                        addButton.text('Add Product');
                    },
                    error: function() {
                        Swal.fire('Error', 'An error occurred while saving the product.', 'error');
                        addButton.prop('disabled', false);
                        addButton.text('Add Product');
                    }
                });
            });




            $('.select2').select2();
           
        });


        // Load Categories and Statuses via AJAX
        function loadCategoriesAndStatuses(callback) {
            // Load Categories
            $.ajax({
                url: '<?= base_url('ProductController/get_categories') ?>', 
                type: 'GET',
                success: function(response) {
                    let categories = JSON.parse(response);
                    let categoryOptions = '<option value="">Select Category</option>';
                    categories.forEach(function(category) {
                        categoryOptions += `<option value="${category.id_kategori}">${category.nama_kategori}</option>`;
                    });
                    $('#kategori_id').html(categoryOptions); // Update the options
                    $('#kategori_id').trigger('change'); // Trigger change to update Select2

                    // Reinitialize Select2 after options are set
                    $('#kategori_id').select2({
                        placeholder: "Select Category",
                        allowClear: true
                    });

                    // Load Statuses
                    $.ajax({
                        url: '<?= base_url('ProductController/get_statuses') ?>', 
                        type: 'GET',
                        success: function(response) {
                            let statuses = JSON.parse(response);
                            let statusOptions = '<option value="">Select Status</option>';
                            statuses.forEach(function(status) {
                                statusOptions += `<option value="${status.id_status}">${status.nama_status}</option>`;
                            });
                            $('#status_id').html(statusOptions); // Update the options
                            $('#status_id').trigger('change'); // Trigger change to update Select2

                            // Reinitialize Select2 after options are set
                            $('#status_id').select2({
                                placeholder: "Select Status",
                                allowClear: true
                            });

                            // Call the callback function after both categories and statuses are loaded
                            if (callback) {
                                callback();
                            }
                        }
                    });
                }
            });
        }

        // Load products into the DataTable
        function loadProducts() {
            $.ajax({
                url: '<?= base_url('ProductController/get_products_ajax') ?>',
                type: 'GET',
                success: function(response) {
                    let products = JSON.parse(response);
                    let rows = '';
                    let index = 1;
                    products.forEach(function(product) {
                        rows += `
                            <tr>
                                <td class="px-6 py-4">${index++}</td>
                                <td class="px-6 py-4">${product.nama_produk}</td>
                                <td class="px-6 py-4">Rp ${parseInt(product.harga).toLocaleString()}</td>
                                <td class="px-6 py-4">${product.nama_kategori}</td>
                                <td class="px-6 py-4">${product.nama_status}</td>
                                <td class="px-6 py-4">
                                    <button class="edit-btn bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600" data-id="${product.id_produk}">Edit</button>
                                    <button class="delete-btn bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" data-id="${product.id_produk}">Delete</button>
                                </td>
                            </tr>
                        `;
                    });

                    // Destroy existing DataTable instance before reinitializing
                    if ($.fn.dataTable.isDataTable('#productTable')) {
                        $('#productTable').DataTable().destroy();
                    }

                    // Update table content
                    $('#productTable tbody').html(rows);

                    // Reinitialize DataTable
                    $('#productTable').DataTable({
                        paging: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 10,
                        lengthMenu: [10, 20, 100],
                        responsive: true,
                    });
                }
            });
        }


        // Edit product
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('ProductController/get_product_by_id/') ?>' + id,
                type: 'GET',
                success: function(response) {
                    // Load categories and statuses first
                    loadCategoriesAndStatuses(function() {
                        // After categories and statuses are loaded, populate the product fields
                        var product = JSON.parse(response);
                        $('#modalTitle').text('Edit Product');
                        $('#productId').val(product.id_produk);
                        $('#nama_produk').val(product.nama_produk);
                        
                        $('#harga').val('Rp ' + parseInt(product.harga).toLocaleString());
                        // Set the values for kategori_id and status_id (Select2 will handle this)
                        $('#kategori_id').val(product.kategori_id).trigger('change'); 
                        $('#status_id').val(product.status_id).trigger('change'); 
                        
                        // Show the modal
                        $('#productModal').removeClass('hidden');
                    });
                }
            });
        });


        // Delete product
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('ProductController/delete_product/') ?>' + id,
                        type: 'GET',
                        success: function(response) {
                            loadProducts();
                            Swal.fire('Deleted!', 'Your product has been deleted.', 'success');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
