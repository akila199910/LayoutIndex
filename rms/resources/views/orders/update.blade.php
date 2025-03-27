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
                    <li class="breadcrumb-item active">Update Orders</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('orders') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form id="submitForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <input type="hidden" name="id" value="{{ $find_order->id }}">
                            <input type="hidden" name="status" value="{{ $find_order->status }}">

                            <div class="col-12">
                                <div class="form-heading mb-3">
                                    <h4>Update Order</h4>
                                </div>
                                <div class="form-heading">
                                    <h4>Date and Time</h4>
                                    <input type="datetime-local"
                                           name="kitchen_time"
                                           id="kitchen_time"
                                           value="{{ \Carbon\Carbon::parse($find_order->kitchen_time)->format('Y-m-d\TH:i') }}">
                                    <small class="text-danger font-weight-bold err_kitchen_time"></small>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-3">
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-heading">
                                            <h5 class="text-info font-size-14 text-uppercase font-weight-bold">
                                                Concessions
                                            </h5>
                                        </div>
                                    </div>

                                    @foreach ($concessions as $item)
                                        @php
                                            $isSelected = isset($selected_items[$item->id]);
                                            $selectedQty = $isSelected ? $selected_items[$item->id]->qty : '';
                                        @endphp

                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                            <div class="profile-check-blk input-block">
                                                <div class="remember-me">
                                                    <label class="custom_check mr-2 mb-0 d-inline-flex remember-me w-100">
                                                        <span class="flex-grow-1">{{ $item->name }}</span>

                                                        <input type="checkbox"
                                                               name="concessions[]"
                                                               class="me-2"
                                                               id="concession_{{ $item->id }}"
                                                               value="{{ $item->id }}"
                                                               onclick="toggleQty({{ $item->id }})"
                                                               {{ $isSelected ? 'checked' : '' }}>

                                                        <span class="checkmark"></span>
                                                    </label>

                                                    <input type="number"
                                                           name="quantities[{{ $item->id }}]"
                                                           id="qty_{{ $item->id }}"
                                                           class="form-control mt-2 qty-input"
                                                           placeholder="Qty"
                                                           min="1"
                                                           value="{{ $selectedQty }}"
                                                           style="width: 80px; {{ $isSelected ? '' : 'display:none;' }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-danger font-weight-bold err_concessions"></small>
                            </div>
                    </div>
                    @if (Auth::user()->hasPermissionTo('Update_Order'))
                    <div class="col-12">
                        <div class="doctor-submit text-end">
                            <button type="submit"
                                class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $('input[type="checkbox"][name="concessions[]"]').each(function () {
            if ($(this).is(':checked')) {
                $('#qty_' + $(this).val()).show();
            }
    });
})
    function toggleQty(id) {
                const checkbox = document.getElementById('concession_' + id);
                const qtyInput = document.getElementById('qty_' + id);

                if (checkbox.checked) {
                    qtyInput.style.display = 'block';
                    qtyInput.required = true;
                } else {
                    qtyInput.style.display = 'none';
                    qtyInput.required = false;
                    qtyInput.value = '';
                }
            }


        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('orders.update') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                                $('#' + key).addClass('is-invalid');
                            }
                        });
                    } else {
                        successPopup(response.message, response.route)
                    }
                },
                statusCode: {
                    401: function() {
                        window.location.href =
                            '{{ route('login') }}'; //or what ever is your login URI
                    },
                    419: function() {
                        window.location.href =
                            '{{ route('login') }}'; //or what ever is your login URI
                    },
                },
                error: function(data) {
                    someThingWrong();
                }
            });
        });

        function errorClear()
        {
            $('#name').removeClass('is-invalid')
            $('.err_name').text('')


        }
    </script>
@endsection
