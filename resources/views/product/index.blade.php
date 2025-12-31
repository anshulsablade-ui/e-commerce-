@extends('layouts.app')
@section('title', 'Product List')

@section('style')
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.5/r-3.0.7/datatables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title">Filter</h5>
                    <div class="d-flex justify-content-between align-items-center row pt-4 gap-6 gap-md-0 g-md-6">
                        <div class="col-md-4 product_status">
                            <select id="filterStatus" class="form-select text-capitalize">
                                <option value="">Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select></div>
                        <div class="col-md-4 product_category">
                            <select id="filterCategory" class="form-select text-capitalize">
                                <option value="">Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 product_stock">
                            <select id="filterStock" class="form-select text-capitalize">
                                <option value="">Stock</option>
                                <option value="Out_of_Stock">Out_of_Stock</option>
                                <option value="In_Stock">In_Stock</option>
                            </select></div>
                    </div>
                </div>

                <div class="card-body table-responsive mb-4 mt-2">
                    <table class="table table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Stock</th>
                                <th>Category</th>
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
                ajax: {
                    url: "{{ route('product.index') }}",
                    data: function (d) {
                        d.category_id = $('#filterCategory').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'image',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `<img src="${data}" width="50" class="rounded">`;
                        }
                    },
                    { data: 'name' },
                    { data: 'price' },
                    { data: 'status' },
                    { data: 'stock' },
                    { data: 'category' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        
            $('#filterCategory, #filterStatus, #filterStock').on('change', function () {
                table.draw();
            });


            $('body').on('click', '.delete', function() {

                var product_id = $(this).attr("data-id");

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
                            url: '/product/' + product_id,

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
