@extends('layouts.app')

@section('title', 'Edit Order')

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
                    <h4 class="fw-bold mb-1">Edit Order</h4>
                </div>
                <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">
                    ← Back to Orders
                </a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">

                <form action="" id="orderForm">
                    @method('put')

                    <div class="card-header border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <select class="form-select select2" style="width: 100%" name="customer_id"
                                    id="customer-select">
                                    <option value="{{ $order->customer->id }}">{{ $order->customer->name }}</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-4">

                        <div id="rowContainer">

                            @foreach ($order->orderItem as $item)
                                <div class="row product-row" data-row="0">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select category-select" name="category_id[]">
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $item->product->category->id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Product</label>
                                        {{-- <select class="form-select product-select" name="product_id[]"></select> --}}
                                        <select class="form-select product-select" name="product_id[]"
                                            data-id="{{ $item->product->id }}" data-text="{{ $item->product->name }}"
                                            data-price="{{ $item->price }}">
                                        </select>

                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control price" name="price[]"
                                            value="{{ $item->product->price }}" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Qty</label>
                                        <input type="number" class="form-control quantity" name="quantity[]"
                                            value="{{ $item->quantity }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Total</label>
                                        <input type="number" class="form-control row-total" name="total[]"
                                            value="{{ $item->quantity * $item->product->price }}" readonly>
                                    </div>

                                    <div class="col-md-3 mb-3 d-flex align-items-end text-end">
                                        <button type="button" class="btn btn-danger removeRow d-none">Remove</button>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-primary" id="addRow">+ Add</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Discount</label>
                                <input type="number" class="form-control" id="discount" name="discount"
                                    value="{{ $order->discount }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Order Status</label>
                                <select class="form-select" name="order_status" id="order_status">
                                    <option value="pending" {{ 'pending' == $order->status ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="processing" {{ 'processing' == $order->status ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="completed" {{ 'completed' == $order->status ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="cancelled" {{ 'cancelled' == $order->status ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <h6>Subtotal: ₹ <span id="subtotal">{{ $order->subtotal }}</span></h6>
                                <h6>Discount: ₹ <span id="discountAmount">{{ $order->discount_amount }}</span></h6>
                                <h6>Grand Total: ₹ <span id="grandTotal">{{ $order->grand_total }}</span></h6>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
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
                                };
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
            prependProductRow();

            function prependProductRow() {
                $('.product-row').each(function(index, element) {
                    initProductSelect($(this));
                    $(this).find('.removeRow').removeClass('d-none');

                });
            }

            function initProductSelect($row) {

                console.log($row);
                let $productSelect = $row.find('.product-select');

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
                                category_id: $row.find('.category-select').val()
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

                let selectedId = $productSelect.data('id');
                let selectedText = $productSelect.data('text');
                let selectedPrice = $productSelect.data('price');

                if (selectedId) {
                    let option = new Option(selectedText, selectedId, true, true);
                    $productSelect.append(option).trigger('change');

                    $row.find('.price').val(selectedPrice);
                    calculateRow($row);
                }

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

            $(document).on('change', ' #discount', function() {
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

            $(document).on('change', '.category-select', function() {
                let row = $(this).closest('.product-row');
                row.find('.product-select').val(null).trigger('change');
                row.find('.price, .row-total').val('');
                calculateGrandTotal();
            });


            // add row
            var rowIndex = 1;
            $('#addRow').click(function() {

                var $row = $(`<div class="row product-row" data-row="${rowIndex}">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select category-select" name="category_id[]">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product</label>
                                    <select class="form-select product-select" name="product_id[]"></select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control price" name="price[]" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" class="form-control quantity" name="quantity[]" value="1">
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

                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('order.update', $order->id) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        window.location.href = "{{ route('order.index') }}";
                    },
                    error: function(xhr) {
                        $.each(xhr.responseText.message, function(key, value) {
                            $(`#${key}`).addClass('is-invalid').after(
                                `<div class="invalid-feedback">${value}</div>`);
                        });
                    }
                });
            });
        });
    </script>
@endsection
