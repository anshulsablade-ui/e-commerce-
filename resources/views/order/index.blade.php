@extends('layouts.app')
@section('title', 'Order List')

@section('style')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.5/r-3.0.7/datatables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Filter</h5>
                        <a href="{{ route('order.create') }}" class="text-white btn btn-primary"><i class="tf-icons bx bx-plus"></i> Add Order</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center row pt-1 gap-6 gap-md-0 g-md-6">
                        <div class="col-md-4 order_status">
                            <select id="filterStatus" class="form-select text-capitalize" name="status">
                                <option value="">Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive mb-4 mt-2">
                    <table class="table table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Date</th>
                                <th>Customers</th>
                                <th>items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
                ajax: {
                    url: "{{ route('order.index') }}",
                    data: function (d) {
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [
                    { data: 'order_number' },
                    { data: 'created_at' },
                    { data: 'customer' },
                    { data: 'items' },
                    { data: 'grand_total' },
                    { data: 'status',
                        render: function (data) {
                            if (data === 'pending') {
                                return `<span class="badge bg-label-warning me-1">Pending</span>`;
                            } else if (data === 'processing') {
                                return `<span class="badge bg-label-info me-1">Processing</span>`;
                            } else if (data === 'completed') {
                                return `<span class="badge bg-label-success me-1">Completed</span>`;
                            } else if (data === 'cancelled') {
                                return `<span class="badge bg-label-danger me-1">Cancelled</span>`;
                            }
                        }
                     },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        
            $('#filterStatus').on('change', function () {
                table.draw();
            });


            $('body').on('click', '.delete', function() {

                var order_id = $(this).attr("data-id");

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
                            url: '/order/' + order_id,

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
