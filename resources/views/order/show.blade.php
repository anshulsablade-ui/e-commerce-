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
                    <div class="d-flex align-items-end">
                        <small class="text-muted">{{ $order->created_at->format('M d, Y, h:i A') }}</small>
                        @if ($order->payment_status == 'paid')
                            <p class="mb-0 ps-2"><b>Payment :</b></p>
                            <span class="badge bg-label-success mx-1">Paid</span>
                        @else
                            <p class="mb-0 ps-2"><b>Payment :</b></p>
                            <span class="badge bg-label-danger mx-1">Pending</span>
                        @endif
                    </div>
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
                    <div class="row d-flex justify-content-between border-top pt-3">
                        <div class="col-auto">
                            @if ($order->payment_status == 'pending')
                                <button type="button" class="btn btn-primary w-100 mb-2" id="paypalPayBtn">
                                    Pay with PayPal
                                    <span class="spinner-border spinner-border-sm ms-2" role="status"
                                        style="display: none;"></span>
                                </button>
                                <button type="button" class="btn btn-primary w-100 mb-2" id="stripePayBtn">
                                    Pay with Stripe
                                    <span class="spinner-border spinner-border-sm ms-2" role="status"
                                        style="display: none;"></span>
                                </button>
                            @endif
                        </div>
                        <div class="col-auto d-flex justify-content-end">

                            <div style="width:260px">
                                <div class="d-flex justify-content-between total-box mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span id="subtotal">₹{{ $order->subtotal }}</span>
                                </div>

                                <div class="d-flex justify-content-between total-box mb-2">
                                    <span class="text-muted">Discount</span>
                                    <span id="discountAmount">₹{{ $order->discount_amount }}</span>
                                </div>

                                <div class="d-flex justify-content-between total-box fw-bold border-top pt-2">
                                    <span>Total</span>
                                    <span id="grandTotal">₹{{ $order->grand_total }}</span>
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
    <script src="https://js.stripe.com/v3/"></script>

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

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // paypal payment
            $('#paypalPayBtn').on('click', function(e) {
                e.preventDefault();

                let amount = '{{ $order->grand_total }}';

                const description = 'Payment for order #{{ $order->order_number }}';
                const order_id = '{{ $order->id }}';
                const $btn = $('#paypalPayBtn');
                const $spinner = $btn.find('.spinner-border');

                $btn.prop('disabled', true);
                $spinner.show();

                $.ajax({
                    url: '{{ route('paypal.create') }}',
                    method: 'POST',
                    data: {
                        amount: amount,
                        description: description,
                        order_id: order_id
                    },
                    success: function(response) {
                        if (response.success && response.approval_url) {
                            window.location.href = response.approval_url;
                        } else {
                            alert('Error creating payment');
                            $btn.prop('disabled', false);
                            $spinner.hide();
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        $btn.prop('disabled', false);
                        $spinner.hide();
                    }
                });
            });


            $('#stripePayBtn').on('click', function(e) {
                e.preventDefault();

                const order_id = '{{ $order->id }}';
                const $btn = $(this);
                const $spinner = $btn.find('.spinner-border');

                $btn.prop('disabled', true);
                $spinner.show();

                $.post("{{ route('stripe.intent') }}", {
                        order_id: order_id,
                        _token: "{{ csrf_token() }}"
                    })
                    .done(function(response) {

                        stripe.confirmCardPayment(response.client_secret, {
                            payment_method: {
                                card: card
                            }
                        }).then(function(result) {

                            if (result.error) {
                                $('#card-errors').text(result.error.message);
                                $btn.prop('disabled', false);
                                $spinner.hide();
                            } else {

                                if (result.paymentIntent.status === 'succeeded') {

                                    $.post("{{ route('stripe.confirm') }}", {
                                        order_id: order_id,
                                        payment_intent_id: result.paymentIntent.id,
                                        amount: result.paymentIntent.amount / 100,
                                        _token: "{{ csrf_token() }}"
                                    });

                                    alert('Payment successful!');
                                    window.location.reload();
                                }
                            }
                        });
                    })
                    .fail(function(xhr) {
                        alert(xhr.responseJSON?.message || 'Payment failed');
                        $btn.prop('disabled', false);
                        $spinner.hide();
                    });
            });


        });
    </script>
@endsection
