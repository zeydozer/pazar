@extends('index')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <!-- content -->
    <div class="row mt-4">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Today
                                Revenue</span>
                            <h2 class="mb-0">$2189</h2>
                        </div>
                        <div class="align-self-center">
                            <div id="today-revenue-chart" class="apex-charts"></div>
                            <span class="text-success font-weight-bold font-size-13"><i
                                    class='uil uil-arrow-up'></i> 10.21%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Product
                                Sold</span>
                            <h2 class="mb-0">1065</h2>
                        </div>
                        <div class="align-self-center">
                            <div id="today-product-sold-chart" class="apex-charts"></div>
                            <span class="text-danger font-weight-bold font-size-13"><i
                                    class='uil uil-arrow-down'></i> 5.05%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">New
                                Customers</span>
                            <h2 class="mb-0">11</h2>
                        </div>
                        <div class="align-self-center">
                            <div id="today-new-customer-chart" class="apex-charts"></div>
                            <span class="text-success font-weight-bold font-size-13"><i
                                    class='uil uil-arrow-up'></i> 25.16%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">New
                                Visitors</span>
                            <h2 class="mb-0">750</h2>
                        </div>
                        <div class="align-self-center">
                            <div id="today-new-visitors-chart" class="apex-charts"></div>
                            <span class="text-danger font-weight-bold font-size-13"><i
                                    class='uil uil-arrow-down'></i> 5.05%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- stats + charts -->
    <div class="row">
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body p-0">
                    <h5 class="card-title header-title border-bottom p-3 mb-0">Overview</h5>
                    <!-- stat 1 -->
                    <div class="media px-3 py-4 border-bottom">
                        <div class="media-body">
                            <h4 class="mt-0 mb-1 font-size-22 font-weight-normal">121,000</h4>
                            <span class="text-muted">Total Visitors</span>
                        </div>
                        <i data-feather="users" class="align-self-center icon-dual icon-lg"></i>
                    </div>

                    <!-- stat 2 -->
                    <div class="media px-3 py-4 border-bottom">
                        <div class="media-body">
                            <h4 class="mt-0 mb-1 font-size-22 font-weight-normal">21,000</h4>
                            <span class="text-muted">Total Product Views</span>
                        </div>
                        <i data-feather="image" class="align-self-center icon-dual icon-lg"></i>
                    </div>

                    <!-- stat 3 -->
                    <div class="media px-3 py-4">
                        <div class="media-body">
                            <h4 class="mt-0 mb-1 font-size-22 font-weight-normal">$21.5</h4>
                            <span class="text-muted">Revenue Per Visitor</span>
                        </div>
                        <i data-feather="shopping-bag" class="align-self-center icon-dual icon-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body pb-0">
                    <ul class="nav card-nav float-right">
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">Today</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">7d</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">15d</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">1m</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">1y</a>
                        </li>
                    </ul>
                    <h5 class="card-title mb-0 header-title">Revenue</h5>

                    <div id="revenue-chart" class="apex-charts mt-3"  dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card">
                <div class="card-body pb-0">
                    <h5 class="card-title header-title">Targets</h5>
                    <div id="targets-chart" class="apex-charts mt-3" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- row -->

    <!-- products -->
    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mt-0 mb-0 header-title">Sales By Category</h5>
                    <div id="sales-by-category-chart" class="apex-charts mb-0 mt-4" dir="ltr"></div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
        <div class="col-xl-7">
            <div class="card">
                <div class="card-body">
                    <a href="" class="btn btn-primary btn-sm float-right">
                        <i class='uil uil-export ml-1'></i> Export
                    </a>
                    <h5 class="card-title mt-0 mb-0 header-title">Recent Orders</h5>

                    <div class="table-responsive mt-4">
                        <table class="table table-hover table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#98754</td>
                                    <td>ASOS Ridley High</td>
                                    <td>Otto B</td>
                                    <td>$79.49</td>
                                    <td><span class="badge badge-soft-warning py-1">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>#98753</td>
                                    <td>Marco Lightweight Shirt</td>
                                    <td>Mark P</td>
                                    <td>$125.49</td>
                                    <td><span class="badge badge-soft-success py-1">Delivered</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#98752</td>
                                    <td>Half Sleeve Shirt</td>
                                    <td>Dave B</td>
                                    <td>$35.49</td>
                                    <td><span class="badge badge-soft-danger py-1">Declined</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#98751</td>
                                    <td>Lightweight Jacket</td>
                                    <td>Shreyu N</td>
                                    <td>$49.49</td>
                                    <td><span class="badge badge-soft-success py-1">Delivered</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#98750</td>
                                    <td>Marco Shoes</td>
                                    <td>Rik N</td>
                                    <td>$69.49</td>
                                    <td><span class="badge badge-soft-danger py-1">Declined</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row -->

</div> <!-- container-fluid -->

@endsection

@section('style')

<!-- plugins -->
<link href="/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('script')

<!-- optional plugins -->
<script src="/assets/libs/moment/moment.min.js"></script>
<script src="/assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="/assets/libs/flatpickr/flatpickr.min.js"></script>

<!-- page js -->
<script src="/assets/js/pages/dashboard.init.js"></script>

@endsection