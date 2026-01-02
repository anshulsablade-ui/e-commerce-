@extends('layouts.app')
@section('title')
Customer: {{ $customer->name }}
@endsection

@section('style')

@endsection

@section('content')
    {{-- <div class="row">
        
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Customer information</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="@if($customer->image) {{ asset('customer/' . $customer->image) }} @else {{ asset('default/default.png') }} @endif" class="img-fluid" alt="{{ $customer->name }}">
                        </div>
                        <div class="col-md-7">
                            <h4>{{ $customer->name }}</h4>
                            <p>Rs: ₹ {{ $customer->email }}</p>
                            <p>Stock: {{ $customer->mobile }}</p>
                            <p>{{ $customer->address }}</p>
                            <a href="{{ route('customer.edit', $customer->id) }}" class="menu-link"><i class="menu-icon tf-icons bx bx-edit"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row align-items-center">

                    <div class="col-md-4 text-center">
                        <img src="{{ $customer->image ? asset('customer/'.$customer->image) : asset('default/default.png') }}" class="rounded-circle img-thumbnail mb-3 mx-auto" alt="{{ $customer->name }}" >
                        <h5 class="mb-0">{{ $customer->name }}</h5>
                    </div>

                    <div class="col-md-8">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Email</label>
                                <p class="fw-semibold mb-0">{{ $customer->email }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Mobile</label>
                                <p class="fw-semibold mb-0">{{ $customer->mobile }}</p>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="text-muted">Address</label>
                                <p class="fw-semibold mb-0">{{ $customer->address }}</p>
                            </div>

                        </div>

                        <div class="mt-3">
                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-edit"></i> Edit Profile
                            </a>

                            <a href="{{ route('customer.index') }}" class="btn btn-outline-secondary btn-sm">
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