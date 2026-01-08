@extends('layouts.app')

@section('title', 'Add Order')

@section('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
@endsection

@section('content')
    <div class="row">

        <div class="col-md-12 pb-4">
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

        <div class="col-lg-12">
            <div class="card">

                <form action="" id="orderForm">

                    <div class="card-header border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <select class="form-select select2" style="width: 100%" name="customer_id"
                                    id="customer-select"></select>
                                <div class="invalid-feedback customer_error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-4">

                        <div id="rowContainer">

                            <div class="row product-row" data-row="0">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select category" name="category[]">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback category_error"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product</label>
                                    <select class="form-select product" name="product[]"></select>
                                    <div class="invalid-feedback product_error"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control price" name="price[]" readonly>
                                    <div class="invalid-feedback price_error"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control quantity" name="quantity[]" value="1">
                                    <div class="invalid-feedback quantity_error"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Total</label>
                                    <input type="number" class="form-control row-total" name="total[]" readonly>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end text-end">
                                    <button type="button" class="btn btn-danger removeRow d-none">Remove</button>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-primary" id="addRow">+ Add</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Discount</label>
                                <input type="number" class="form-control" id="discount" name="discount">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Order Status</label>
                                <select class="form-select" name="order_status" id="order_status">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            {{-- <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button id="custom-paypal-btn" class="btn btn-success">💳 Pay Now</button>
                            </div> --}}
                        </div>

                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <div id="paypal-button-container" class="col-md-3 mb-3 d-flex align-items-end text-end">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-end">
                                    <h6>Subtotal: ₹ <span id="subtotal">0.00</span></h6>
                                    <h6>Discount: ₹ <span id="discountAmount">0.00</span></h6>
                                    <h6>Grand Total: ₹ <span id="grandTotal">0.00</span></h6>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency=USD"></script>

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // customer
            $('#customer-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search Customer',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('ajax.customer') }}",
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name,
                                    email: item.email,
                                    image: item.image
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: formatCustomer,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            function formatCustomer(customer) {
                if (!customer.id) {
                    return customer.text;
                }

                let image = customer.image ? customer.image : '/default/default.png';

                return `<div class="d-flex align-items-center gap-3">
                            <img src="${image}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            <div>
                                <div class="fw-semibold">${customer.text}</div>
                                <small class="text-muted">${customer.email ?? ''}</small>
                            </div>
                        </div>`;
            }


            initProductSelect($('.product-row').first());


            function initProductSelect($row) {

                let $productSelect = $row.find('.product');

                $productSelect.select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select Product',
                    ajax: {
                        url: "{{ route('ajax.product') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term,
                                category_id: $row.find('.category').val()
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name,
                                        price: item.price,
                                        image: item.image
                                    };
                                })
                            };
                        }
                    },
                    templateResult: formatProduct,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });

                // product selected
                $productSelect.on('select2:select', function(e) {
                    $row.find('.price').val(e.params.data.price);
                    calculateRow($row);
                });
            }

            function formatProduct(product) {
                if (!product.id) {
                    return product.text;
                }

                return `<div class="d-flex align-items-center gap-3">
                            <img src="${product.image}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                            <div>
                                <div class="fw-semibold">${product.text}</div>
                                <small class="text-muted">₹ ${product.price}</small>
                            </div>
                        </div>`;
            }

            $(document).on('keyup change', '#discount', function() {
                calculateGrandTotal();
            });


            function calculateRow(row) {
                let price = parseFloat(row.find('.price').val()) || 0;
                let qty = parseInt(row.find('.quantity').val()) || 1;
                row.find('.row-total').val((price * qty).toFixed(2));
                calculateGrandTotal();
            }


            $(document).on('keyup change', '.quantity', function() {
                calculateRow($(this).closest('.product-row'));
            });

            function calculateGrandTotal() {
                let total = 0;
                $('.row-total').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });


                let discount = (total * $('#discount').val()) / 100;
                $('#subtotal').text(total.toFixed(2));
                $('#discountAmount').text(discount.toFixed(2));
                $('#grandTotal').text((total - discount).toFixed(2));
            }

            $(document).on('change', '.category', function() {
                let row = $(this).closest('.product-row');
                row.find('.product').val(null).trigger('change');
                row.find('.price, .row-total').val('');
                calculateGrandTotal();
            });


            // add row
            var rowIndex = 1;
            $('#addRow').click(function() {

                var $row = $(`<div class="row product-row" data-row="${rowIndex}">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select category" name="category[]">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback category_error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product</label>
                                    <select class="form-select product" name="product[]"></select>
                                    <div class="invalid-feedback product_error"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control price" name="price[]" readonly>
                                    <div class="invalid-feedback price_error"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control quantity" name="quantity[]" value="1">
                                    <div class="invalid-feedback quantity_error"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Total</label>
                                    <input type="number" class="form-control row-total" name="total[]" readonly>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end text-end">
                                    <button type="button" class="btn btn-danger removeRow d-none">Remove</button>
                                </div>
                            </div>`);
                rowIndex++;
                $('#rowContainer').append($row);
                initProductSelect($row);
                $('.removeRow').removeClass('d-none');
            });

            // remove row
            $(document).on('click', '.removeRow', function() {
                $(this).closest('.product-row').remove();
                $.each($('.product-row'), function(index, row) {
                    $(row).attr('data-row', index);
                    $(row).find('span.error-text').attr('data-index', index);
                    rowIndex = index + 1;
                })
                if ($('.product-row').length === 1) {
                    $('.removeRow').addClass('d-none');
                }
                calculateGrandTotal();
            });

            // form submit
            $('#orderForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                ajaxCall("{{ route('order.store') }}", "POST", formData, function(response) {
                        if (response.status === 'success') {
                            window.location.href = "{{ route('order.index') }}";
                        }
                    },
                    function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {

                                // Customer
                                if (key === 'customer_id') {
                                    $('#customer-select').addClass('is-invalid');
                                    $('.customer_error').text(value[0]);
                                }
                                if (key === 'product') {
                                    $('.product').addClass('is-invalid');
                                    $('.product_error').text(value[0]);
                                }

                                if (key.includes('.')) {
                                    let parts = key.split('.');
                                    let field = parts[0];
                                    let index = parts[1];

                                    let row = $(`.product-row[data-row="${index}"]`);

                                    row.find(`.${field}`).addClass('is-invalid');
                                    row.find(`.${field}_error`).text(value[0]);
                                }
                            });
                        }
                    }
                );

            });
        });
    </script>

    <script>
        paypal.Buttons({
            createOrder: function() {
    let amount = $('#grandTotal').text().trim(); // ✅ FIX

    // Convert to PayPal format
    amount = parseFloat(amount).toFixed(2);

    console.log('PayPal Amount:', amount);

                return $.ajax({
                    url: '/paypal/create-order',
                    method: 'POST',
                    data: { amount: amount }
                }).then(function(response) {
                    console.log('Order Response:', response.id);
                    return response.id;
                });
            },

            onApprove: function(data) {
                return $.ajax({
                    url: '/paypal/capture-order',
                    method: 'POST',
                    data: { orderID: data.orderID }
                }).then(function(res) {
                    alert('Payment Successful!');
                    console.log(res);
                });
            },

            onCancel: function() {
                alert('Payment cancelled');
            },

            onError: function(err) {
                console.log(err);
                if (err.status == 400) {
                    alert(err.message);
                }
                alert('Payment failed');
            }
        }).render('#paypal-button-container');
    </script>

@endsection
