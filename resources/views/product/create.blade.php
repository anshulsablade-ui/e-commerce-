@extends('layouts.app')
@section('title', 'Product')

@section('style')

@endsection

@section('content')
    <div class="row">

        <!-- Form controls -->
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Product information</h5>
                <div class="card-body">
                    <form id="productForm" enctype="multipart/form-data">
                        csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="name@example.com" />
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Product Slug</label>
                            <input class="form-control" type="text" id="slug" name="slug" placeholder="Slug here..." />
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input class="form-control" type="file" id="images" name="images[]" placeholder="" />
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">description</label>
                            <textarea class="form-control" name="description" id="description" cols="30"
                                rows="10"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" name="price" id="price" value="" />
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="text" class="form-control" name="stock" id="stock" value="" />
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlSelect2" class="form-label">Example multiple select</label>
                            <select class="form-select" id="exampleFormControlSelect2" name="category_id"
                                aria-label="Multiple select example">
                                <option selected>Open this select menu</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    {{-- <script>
        $(document).ready(function () {
            ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#productForm').submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('product.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }

                });
            });
        });
    </script> --}}

    <script>
        $(document).ready(function () {
            // Auto-generate slug from product name
            $('#name').on('input', function () {
                let name = $(this).val().trim();
                let slug = name
                    .toLowerCase()
                    .replace(/[^a-z0-9-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            });

            $('#productForm').submit(function (e) {
                e.preventDefault();

                // Reset previous error states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                let formData = new FormData(this);
                let submitBtn = $('#submitBtn');
                let spinner = submitBtn.find('.spinner-border');

                // Disable button and show spinner
                submitBtn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('product.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || 'Product created successfully!');
                            // Optional: redirect or reset form
                            window.location.href = "{{ route('product.index') }}";
                        }
                    },
                    error: function (xhr) {
                        let response = xhr.responseJSON;

                        if (xhr.status === 422 && response.errors) {
                            // Laravel validation errors
                            $.each(response.errors, function (field, messages) {
                                let input = $(`[name="${field}"], [name="${field}[]"]`).first();
                                input.addClass('is-invalid');

                                let feedback = $('<div class="invalid-feedback"></div>');
                                $.each(messages, function (i, msg) {
                                    feedback.append(msg + '<br>');
                                });
                                input.after(feedback);
                            });

                            toastr.error('Please fix the errors below.');
                        } else {
                            toastr.error(response.message || 'Something went wrong!');
                        }
                    },
                    complete: function () {
                        // Re-enable button and hide spinner
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection