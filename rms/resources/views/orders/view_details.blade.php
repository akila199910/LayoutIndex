@extends('layouts.sidebar')

@section('title')
 Manage Orders
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('orders') }}">Manage Orders</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Order Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('orders') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="doctor-personals-grp">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="doctor-table-blk mb-4 pt-2">
                                            <h3 class="text-uppercase">Order Details</h3>
                                        </div>
                                        <div class="row  align-items-center">
                                            <div class="col-xl-8 col-md-8">

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Order Ref No</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $orders->ref_no ? $orders->ref_no : 'N/A' }}</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Total Price</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $orders->total_price ? $orders->total_price : 'N/A' }}</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Discount Amount</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $orders->discount_amount ? $orders->discount_amount : 'N/A' }}</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Created By</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $orders->createdBy ? $orders->createdBy->name : 'N/A' }}</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Status</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4 mb-3">
                                                        <div class="detail-personal">
                                                            @php
                                                            $statuses = [
                                                                0 => ['label' => 'Pending', 'class' => 'custom-badge status-red'],
                                                                1 => ['label' => 'In progress', 'class' => 'custom-badge status-yellow'],
                                                                2 => ['label' => 'Completed', 'class' => 'custom-badge status-green'],
                                                            ];

                                                            $statusInfo = $statuses[$orders->status] ?? ['label' => 'Unknown', 'class' => 'custom-badge'];
                                                        @endphp

                                                        <h3>
                                                            <span class="{{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                                                        </h3>

                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <h3 class="text-uppercase mb-3">Order Items</h3>
                                                    @foreach ($orders->orderItems as $orderItem)
                                                        <div class="row mb-3">
                                                            <div class="col-xl-4 col-md-4">
                                                                <div class="detail-personal">
                                                                    <h2>{{ $orderItem->concession_info ? $orderItem->concession_info->name : 'N/A' }}</h2>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-4 col-md-4">
                                                                <div class="detail-personal">
                                                                    <h3>{{ $orderItem->qty ? $orderItem->qty : 'N/A' }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

