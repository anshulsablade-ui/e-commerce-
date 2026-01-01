@extends('layouts.app')
@section('title', 'Category List')

@section('style')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.5/r-3.0.7/datatables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Category List</h5>
                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd">
                            <i class="tf-icons bx bx-plus"></i>
                            Add Category
                        </button>
                    </div>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
                    <div class="offcanvas-header">
                        <h5 id="offcanvasEndLabel" class="offcanvas-title">Add Category</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body my-auto mx-0 flex-grow-0">
                        <form id="categoryForm">
                            <input type="hidden" id="category_id" name="category_id">

                            <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug">
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                    </div>
                </div>

                <div class="card-body table-responsive mb-4 mt-2">
                    <table class="table table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Categories</th>
                                <th>Total Product</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/v/bs5/dt-2.3.5/r-3.0.7/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('category.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'total_product'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            $('#name').on('input', function() {
                let name = $(this).val().trim();
                let slug = name
                    .toLowerCase()
                    .replace(/[^a-z0-9-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            });


            $('body').on('click', '.edit', function() {
                let category_id = $(this).data('id');

                $.get(`/category/edit/${category_id}`, function(response) {
                    if (response.status === 'success') {

                        let data = response.data;

                        $('#category_id').val(data.id);
                        $('#name').val(data.name);
                        $('#slug').val(data.slug);

                        $('#offcanvasEnd').offcanvas('show');
                    }
                });
            });



            $('#categoryForm').submit(function(e) {
                e.preventDefault();

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let categoryId = $('#category_id').val();

                let url = categoryId ? `/category/${categoryId}` : "{{ route('category.store') }}";

                let formData = new FormData(this);

                if (categoryId) {
                    formData.append('_method', 'PUT'); // Laravel update
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#offcanvasEnd').offcanvas('hide');
                        $('#categoryForm')[0].reset();
                        $('#category_id').val('');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key)
                                .addClass('is-invalid')
                                .after(
                                    `<div class="invalid-feedback">${value[0]}</div>`);
                        });
                    }
                });
            });



            $('#offcanvasEnd').on('hidden.bs.offcanvas', function() {
                $('#categoryForm')[0].reset();
                $('#category_id').val('');
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });


            //delete category
            $('body').on('click', '.delete', function() {

                var category_id = $(this).attr("data-id");

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "delete",
                            url: '/category/' + category_id,

                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.success,
                                    'success'
                                )
                                table.ajax.reload();
                            }
                        });
                    }
                })
            });
        });
    </script>

@endsection
