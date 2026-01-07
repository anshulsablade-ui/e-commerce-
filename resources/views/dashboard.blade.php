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
                        <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
                        <div id="revenueChartLoader" class="text-center my-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div id="revenueChart" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-md-12">
                        <h5 class="card-header m-0 me-2 pb-3">Order Status</h5>
                        <div id="orderStatusChartLoader" class="text-center my-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="orderStatusChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('#revenueChartLoader').show();
            $('#orderStatusChartLoader').show();
            $('#revenueChart').hide();
            $('#orderStatusChart').hide();

            $.ajax({
                url: "{{ route('revenue.chart') }}",
                method: "GET",

                success: function(response) {
                    $('#revenueChartLoader').hide();
                    $('#revenueChart').show();

                    var options = {
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                            name: 'Total Revenue',
                            data: response
                        }],
                        xaxis: {
                            categories: [
                                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                            ]
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return '₹' + val.toLocaleString();
                                }
                            }
                        }
                    };

                    new ApexCharts(document.querySelector("#revenueChart"),
                        options
                    ).render();
                }
            });


            $.ajax({
                type: "{{ route('order.status.chart') }}",
                method: "get",
                success: function(response) {
                    $('#orderStatusChartLoader').hide();
                    $('#orderStatusChart').show();

                    let labels = [];
                    let series = [];

                    response.forEach(item => {
                        labels.push(item.status);
                        series.push(item.total);
                    });

                    new ApexCharts(document.querySelector("#orderStatusChart"), {
                        chart: {
                            type: 'donut',
                            height: 300
                        },
                        labels: labels,
                        series: series
                    }).render();
                }
            });
        });
    </script>

    <script>
        $(function() {


            // order status
            $.getJSON('/charts/order-status', function(response) {

                let labels = [];
                let series = [];

                response.forEach(item => {
                    labels.push(item.status);
                    series.push(item.total);
                });

                new ApexCharts(document.querySelector("#orderStatusChart"), {
                    chart: {
                        type: 'donut',
                        height: 300
                    },
                    labels: labels,
                    series: series
                }).render();
            });

        });
    </script>
@endsection
