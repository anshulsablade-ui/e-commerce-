@extends('layouts.app')

@section('title', 'Add Order')

@section('style')
    <style>
        .card {
    border-radius: 12px;
}

.table th {
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.form-label {
    font-weight: 500;
}

    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="row g-4">

    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Create Order</h4>
                <p class="text-muted mb-0">Add a new customer order</p>
            </div>
            <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">
                ← Back to Orders
            </a>
        </div>
    </div>

    <form id="orderForm">

        <!-- LEFT COLUMN -->
        <div class="col-lg-8">

            <!-- Customer Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Customer Information</h6>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="customer_name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Order Date</label>
                            <input type="date" class="form-control" name="order_date" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="customer_email">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="customer_phone">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Order Items Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Order Items</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addItem">
                        + Add Item
                    </button>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th width="120">Price</th>
                                <th width="100">Qty</th>
                                <th width="140">Total</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody id="orderItems">
                            <tr>
                                <td>
                                    <select class="form-select" name="products[]">
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="price[]">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="qty[]" value="1" min="1">
                                </td>
                                <td>
                                    <input type="text" class="form-control bg-light" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        ×
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4">

            <div class="card shadow-sm border-0 position-sticky" style="top: 20px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">Order Summary</h6>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label text-muted">Subtotal</label>
                        <input type="text" class="form-control bg-light" name="subtotal" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Discount</label>
                        <input type="text" class="form-control" name="discount" value="0">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Amount</label>
                        <input type="text" class="form-control fw-bold fs-5 bg-light" name="total" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Order Status</label>
                        <select class="form-select" name="status">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Create Order
                    </button>

                </div>
            </div>

        </div>

    </form>
</div>


@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection