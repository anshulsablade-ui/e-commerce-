@extends('layouts.app')
@section('title')
Product: {{ $product->name }}
@endsection

@section('style')

@endsection

@section('content')
    <div class="row">
        
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Product information</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="@if($product->images->first()) {{ asset('product/' . $product->images->first()->image) }} @endif" class="img-fluid" alt="{{ $product->name }}">
                        </div>
                        <div class="col-md-7">
                            <h4>{{ $product->name }}</h4>
                            <p>Rs: ₹ {{ $product->price }}</p>
                            <p>Stock: {{ $product->stock }}</p>
                            <p>{{ $product->description }}</p>
                            <a href="{{ route('product.edit', $product->id) }}" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

@endsection