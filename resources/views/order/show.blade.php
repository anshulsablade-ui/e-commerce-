@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('style')
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12 mb-4">
            <div
                class="col-md-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">Order #{{ $order->order_number }}</h4>
                    <small class="text-muted">{{ $order->created_at->format('M d, Y, h:i A') }}</small>
                </div>
                <a href="{{ route('order.delete', $order->id) }}" class="btn btn-danger delete">Delete Order</a>
            </div>
        </div>

        <!-- LEFT : Order Details -->
        <div class="col-lg-8">
            <div class="card order-card mb-4">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Order Details</h5>
                        <a href="{{ route('order.edit', $order->id) }}" class="link text-primary">Edit</a>
                    </div>
                </div>
                <div class="card-body">

                    <table class="table align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>PRODUCTS</th>
                                <th class="text-center">PRICE</th>
                                <th class="text-center">QTY</th>
                                <th class="text-end">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItem as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset('product/' . ($item->product->image ?? 'no-image.png')) }}"
                                                class="img-fluid" width="40" height="40"
                                                alt="{{ $item->product->name }}">

                                            <div>
                                                <div class="product-name">
                                                    {{ $item->product->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center price">₹{{ $item->price }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end price">₹{{ $item->price * $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Totals -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-end">
                            <div style="width:260px">
                                <div class="d-flex justify-content-between total-box mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span>₹{{ $order->subtotal }}</span>
                                </div>

                                <div class="d-flex justify-content-between total-box mb-2">
                                    <span class="text-muted">Discount</span>
                                    <span>₹{{ $order->discount_amount }}</span>
                                </div>

                                <div class="d-flex justify-content-between total-box fw-bold border-top pt-2">
                                    <span>Total</span>
                                    <span>₹{{ $order->grand_total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT : Customer Details -->
        <div class="col-lg-4">
            <div class="card order-card mb-4">
                <div class="card-body">

                    <h6 class="mb-3">Customer details</h6>

                    <div class="d-flex justify-content-start align-items-center mb-3">
                        <div class="avatar me-3">
                            <img src="{{ asset('customer/' . $order->customer->image) ?? asset('default/default.png') }}"
                                alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{ route('customer.show', $order->customer->id) }}" class="text-body text-nowrap">
                                <h6 class="mb-0">{{ $order->customer->name }}</h6>
                            </a>
                            <span>Customer ID: #{{ $order->customer->id }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center mb-3">
                        <span
                            class="avatar rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center"><i
                                class="icon-base bx bx-cart icon-lg"></i></span>
                        <h6 class="text-nowrap mb-0">{{ $order->customer->orders()->count() }} Orders</h6>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Contact info</strong>
                            <a href="{{ route('customer.edit', $order->customer->id) }}"
                                class="text-primary text-decoration-none">Edit</a>
                        </div>
                        <p class="mb-1">Email: {{ $order->customer->email }}</p>
                        <p class="mb-0">Mobile: {{ $order->customer->mobile }}</p>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>
        $('.delete').on('click', function(e) {
            e.preventDefault();

            var url = $(this).attr("href");

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
                        url: url,

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
    </script>
@endsection
