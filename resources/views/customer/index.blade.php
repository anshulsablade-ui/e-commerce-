@extends('layouts.app')
@section('title', 'Customer List')

@section('style')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.5/r-3.0.7/datatables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Customer List</h5>
                        <a href="{{ route('customer.create') }}" class="text-white btn btn-primary"><i
                                class="tf-icons bx bx-plus"></i> Add Customer</a>
                    </div>
                </div>

                <div class="card-body table-responsive mb-4 mt-2">
                    <table class="table table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>City</th>
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
                ajax: "{{ route('customer.index') }}",
                columns: [
                    { data: 'id', name: 'id', searchable: false },
                    {
                        data: 'customer',
                        render: function(data) {
                            return `<div class="d-flex align-items-center gap-2">
                                        <img src="${data.image}" width="40" height="40" class="rounded-circle object-fit-cover">
                                        <span class="fw-semibold">${data.name}</span>
                                    </div>`;
                        }
                    },
                    { data: 'email', name: 'email' },
                    { data: 'country', name: 'country' },
                    { data: 'city', name: 'city' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $('body').on('click', '.delete', function() {

                var customer_id = $(this).attr("data-id");

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
                            url: '/customer/' + customer_id,

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
