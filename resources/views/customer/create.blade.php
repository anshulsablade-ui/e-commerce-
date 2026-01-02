@extends('layouts.app')
@section('title', 'Add Customer')

@section('style')

@endsection

@section('content')
    <div class="row">

        <div class="col-md-12 mb-4">
            <div class="col-md-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center row-gap-4">
              <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Add a new Customer</h4>
              </div>
            </div>
        </div>

        <!-- Form controls -->
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Customer information</h5>
                <div class="card-body">
                    <form id="customerForm" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="name" name="name"/>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="email" id="email" name="email" />
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input class="form-control" type="text" id="address" name="address" />
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input class="form-control" type="text" id="mobile" name="mobile" />
                            </div>
    
                            <div class="col">
                                <label for="image" class="form-label">Profile Image</label>
                                <input class="form-control" type="file" id="image" name="image" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select" id="country" name="country" aria-label="Multiple select example">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="city" class="form-label">City</label>
                                <select class="form-select" id="city" name="city" aria-label="Multiple select example">
                                    <option value="">Select City</option>
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

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#country').on('change', function () {
                var country_id = $(this).val();
                if (country_id) {
                    $.ajax({
                        url: "{{ url('/get-cities') }}/" + country_id,
                        type: "GET",
                        dataType: "json",
                        success: function (res) {
                            if (res) {
                                $('#city').empty();
                                $('#city').append('<option value="">Select City</option>');
                                $.each(res, function (key, value) {
                                    $('#city').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            } else {
                                $('#city').empty();
                            }
                        }
                    });
                } else {
                    $('#city').empty();
                }
            });

            $('#customerForm').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('customer.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        window.location.href = "{{ route('customer.index') }}";
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