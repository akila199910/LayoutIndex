@extends('layouts.sidebar')

@section('title')
    Dashboard
@endsection

@section('content')

    <div class="doctor-list-blk col-12">
        <div class="row pb-3">
            <div class="col-xl-6 col-md-6">
                <div class="doctor-table-blk">
                    @php $todayDate = now()->format('Y-F-d'); @endphp
                    <h3 class="text-uppercase">Today Orders {{ $todayDate }}</h3>
                </div>
            </div>
        </div>

        <div class="row pb-3">
            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $toDayTotalOrders }}</h4>
                        <h5>Total</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $toDayPendingOrders }}</h4>
                        <h5>Pending</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 0)"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-spinner fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $toDayProgressOrders }}</h4>
                        <h5>In Progress</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 1)"
                @endif>
                <div class="doctor-widget">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-check fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $toDayCompleteOrders }}</h4>
                        <h5>Completed</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row _upcoming_order_div"></div>
    </div>

    <div class="doctor-list-blk col-12">
        <div class="row pb-3">
            <div class="col-xl-6 col-md-6">
                <div class="doctor-table-blk">
                    <h3 class="text-uppercase">Upcoming Orders</h3>
                </div>
            </div>
        </div>

        <div class="row pb-3">
            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $upComingTotalOrders }}</h4>
                        <h5>Total</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $upComingPendingOrders }}</h4>
                        <h5>Pending</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 0)"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-spinner fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $upComingProgressOrders }}</h4>
                        <h5>In Progress</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 1)"
                @endif>
                <div class="doctor-widget">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-check fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $upComingCompleteOrders }}</h4>
                        <h5>Completed</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row _upcoming_order_div"></div>
    </div>

    <div class="doctor-list-blk col-12">
        <div class="row pb-3">
            <div class="col-xl-6 col-md-6">
                <div class="doctor-table-blk">
                    <h3 class="text-uppercase">ALL Orders</h3>
                </div>
            </div>
        </div>

        <div class="row pb-3">
            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $allTotalOrders }}</h4>
                        <h5>Total</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future','')"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-utensils fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $allPendingOrders }}</h4>
                        <h5>Pending</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 0)"
                @endif>
                <div class="doctor-widget border-right-bg">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-spinner fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $allProgressOrders }}</h4>
                        <h5>In Progress</h5>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12"
                @if (Auth::user()->hasPermissionTo('Read_Order'))
                    style="cursor: pointer" onclick="get_order_List('future', 1)"
                @endif>
                <div class="doctor-widget">
                    <div class="doctor-box-icon flex-shrink-0">
                        <i class="fa-solid fa-check fa-xl" style="color: #ffffff;"></i>
                    </div>
                    <div class="doctor-content dash-count flex-grow-1">
                        <h4>{{ $allCompleteOrders }}</h4>
                        <h5>Completed</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row _upcoming_order_div"></div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_Order'))
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">

                    <div class="page-table-header mb-2">
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3 class="text-uppercase">Today Orders</h3>
                                </div>
                            </div>
                            <div class="col-auto text-end float-end ms-auto download-grp">
                                @if (Auth::user()->hasPermissionTo('Create_Order'))
                                    <a href="{{ route('orders.create.form') }}"
                                        class="btn btn-primary ms-2">
                                        +&nbsp;New Order
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped " id="data_table">
                            <thead>
                                <tr>
                                    <th style="width: 20px">#</th>
                                    <th>Ref No</th>
                                    <th>Total Price</th>
                                    <th>Discount</th>
                                    <th>Kitchen Time</th>
                                    <th>Status</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')

<script>
    var table;

    $(document).ready(function() {
        loadData();

        $('#filter').click(function() {
            table.ajax.reload();
        });
    });

        function loadData() {
                table = $('#data_table').DataTable({
                    "stripeClasses": [],
                    "lengthMenu": [10, 20, 50],
                    "pageLength": 10,
                    processing: true,
                    serverSide: true,
                    orderable: false,
                    ajax: {
                        url: "{{ route('orders', ['json' => 1]) }}"
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'ref_no',
                            name: 'ref_no',
                            orderable: false,
                        },

                        {
                            data: 'total_price',
                            name: 'total_price',
                            orderable: false,
                        },
                        {
                            data: 'discount_amount',
                            name: 'discount_amount',
                            orderable: false,
                        },
                        {
                            data: 'kitchen_time',
                            name: 'kitchen_time',
                            orderable: false,
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
            });
        }

</script>
@endsection
