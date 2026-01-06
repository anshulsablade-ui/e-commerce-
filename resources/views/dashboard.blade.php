@extends('layouts.app')
@section('title', 'Dashboard')

@section('style')

@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 order-1">
            <div class="row">
                <div class="col-lg-3 col-md-12 col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">Total Orders</span>
                            <h4 class="card-title mb-2">{{ $totalOrders }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">Total Sales</span>
                            <h4 class="card-title mb-2">₹{{ $totalSales }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">Customers</span>
                            <h4 class="card-title mb-2">{{ $customers }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1">Products</span>
                            <h4 class="card-title mb-2">{{ $products }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-md-12">
                        <h5 class="card-header m-0 me-2 pb-3">Profit vs Revenue</h5>
                        <div id="profitRevenueChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-md-12">
                        <h5 class="card-header m-0 me-2 pb-3">Order Status</h5>
                        <div id="orderStatusChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {

            $.getJSON('/charts/order-status', function(res) {

                let labels = [];
                let series = [];

                res.forEach(item => {
                    labels.push(item.status);
                    series.push(item.total);
                });

                new ApexCharts(document.querySelector("#orderStatusChart"), {
                    chart: {
                        type: 'pie',
                        height: 300
                    },
                    labels: labels,
                    series: series
                }).render();
            });

            $.getJSON('/charts/profit-vs-revenue', function(res) {

                let months = [];
                let revenue = [];
                let profit = [];

                res.forEach(item => {
                    months.push('Month ' + item.month);
                    revenue.push(item.revenue);
                    profit.push(item.profit);
                });

                let options = {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                            name: 'Revenue',
                            data: revenue
                        },
                        {
                            name: 'Profit',
                            data: profit
                        }
                    ],
                    xaxis: {
                        categories: months
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 5
                    },
                    legend: {
                        position: 'top'
                    }
                };

                new ApexCharts(document.querySelector("#profitRevenueChart"),
                    options
                ).render();
            });

        });
    </script>
@endsection
