@extends('layouts.app')
@section('title', 'Product')

@section('style')

@endsection

@section('content')
    <div class="row">

        <div class="col-md-12 mb-4">
            <div class="col-md-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center row-gap-4">
              <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Add a new Product</h4>
              </div>
              {{-- <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    <button class="btn btn-secondary">Discard</button> 
                    <button class="btn btn-primary">Save draft</button>
                </div>
                <button type="submit" class="btn btn-primary">Publish product</button>
              </div> --}}
            </div>
        </div>

        <!-- Form controls -->
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Product information</h5>
                <div class="card-body">
                    <form id="productForm" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="name@example.com" />
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Product Slug</label>
                            <input class="form-control" type="text" id="slug" name="slug" placeholder="Slug here..." />
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">description</label>
                            <textarea class="form-control" name="description" id="description" cols="30"
                                rows="6"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Images</label>
                            <input class="form-control" type="file" id="images" name="images[]" placeholder="" multiple />
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="price" class="form-label">Price</label>
                                <input type="text" class="form-control" name="price" id="price" value="" />
                            </div>
                            <div class="col">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="text" class="form-control" name="stock" id="stock" value="" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" aria-label="Multiple select example">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" aria-label="Multiple select example">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
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
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


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
                $('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('product.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        window.location.href = "{{ route('product.index') }}";
                    },
                    error: function (response) {
                        console.log(response, 'error');
                        var response = JSON.parse(response.responseText);
                        console.log(response.message);
                        $.each(response.message, function (key, value) {
                            var data = `<div class="invalid-feedback">${value}</div>`;
                            $(`#${key}`).addClass('is-invalid').after(data);
                        });
                    }

                });
            });
        });
    </script>
@endsection