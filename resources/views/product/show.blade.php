@extends('layouts.app')
@section('title')
    Product: {{ $product->name }}
@endsection

@section('style')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Information</h5>
                </div>

                <div class="card-body">
                    <div class="row align-items-start">

                        <div class="col-md-5 text-center">
                            <img src="{{ $product->images->first()
                                ? asset('product/' . $product->images->first()->image)
                                : asset('product/no-image.png') }}"
                                class="img-fluid rounded border mb-3" style="max-height: 320px" alt="{{ $product->name }}">
                        </div>

                        <div class="col-md-7">
                            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>
                            <div class="d-flex gap-4">
                                <h5 class="mb-3">₹ {{ number_format($product->price, 2) }}</h5>
                                <div class="mb-3">
                                    @if ($product->stock > 0)
                                        <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-muted mb-1">Description</h6>
                                <p class="mb-0">{{ $product->description }}</p>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-edit"></i> Edit Product
                                </a>

                                <a href="{{ route('product.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bx bx-arrow-back"></i> Back
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
