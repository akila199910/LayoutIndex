@extends('layouts.sidebar')

@section('title')
    Manage Users
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('users') }}">Manage Users </a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Create new User</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('users') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Create New User</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>First Name <span class="login-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control first_name " id="first_name"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_first_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Last Name <span class="login-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control last_name" id="last_name"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_last_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Email <span class="login-danger">*</span></label>
                                    <input type="text" name="email" class="form-control email" id="email"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_email"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Contact <span class="login-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control contact number_only_val"
                                        id="contact" maxlength="10">
                                    <small class="text-danger font-weight-bold err_contact"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label">Status Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status" checked class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Give Permission</h4>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="row">
                                    @foreach ($permission_list as $premission => $action)
                                        <div class="col-12 mt-2">
                                            <div class="form-heading">
                                                <h5 class="text-info font-size-14 text-uppercase font-weight-bold">

                                                        {{ Str::headline($premission) }}

                                                </h5>
                                            </div>
                                        </div>

                                        @foreach ($action as $act)
                                            @php
                                                $split_permission = explode('_', $act);
                                                $final_action = implode('', $split_permission);
                                                $final_action = Str::headline($final_action);
                                            @endphp
                                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                                <div class="profile-check-blk input-block">
                                                    <div class="remember-me">
                                                        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me ">
                                                            {{ $final_action }}
                                                            <input type="checkbox" name="permissions[]"
                                                                class="permissions_check" id="{{ $act }}"
                                                                data-action="{{ $act }}"
                                                                data-permission="{{ $premission }}"
                                                                value="{{ $act }}">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                                <small class="text-danger font-weight-bold err_permissions"></small>
                            </div>

                            @if (Auth::user()->hasPermissionTo('Create_User'))
                                <div class="col-12">
                                    <div class="doctor-submit text-end">
                                        <button type="submit"
                                            class="btn btn-primary text-uppercase submit-form me-2">Save</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
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
            });

            $('#submitForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData($('#submitForm')[0]);

                $.ajax({
                    type: "POST",
                    beforeSend: function() {
                        $('#loader').show()
                    },
                    url: "{{ route('users.create') }}",
                    data: formData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $('#loader').hide()

                        clearError();

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

            function clearError() {
                $('#first_name').removeClass('is-invalid');
                $('.err_first_name').text('');

                $('#last_name').removeClass('is-invalid');
                $('.err_last_name').text('');

                $('#email').removeClass('is-invalid');
                $('.err_email').text('');

                $('#contact').removeClass('is-invalid');
                $('.err_contact').text('');

                $('.err_permissions').text('');
            }

        });


        $('.permissions_check').change(function() {
            if ($(this).is(':checked')) {
                // Checkbox is checked
                console.log('Checkbox is checked');
                var permission = $(this).attr('data-permission');
                console.log(permission);

                $('#Read_' + permission).prop('checked', true)
            } else {
                // Checkbox is not checked
                console.log('Checkbox is not checked');
                var permission = $(this).attr('data-permission');
                var action = $(this).attr('data-action');
                console.log(action);
                if (action == 'Read_' + permission) {
                    $('#Create_' + permission).prop('checked', false);
                    $('#Update_' + permission).prop('checked', false);
                    $('#Delete_' + permission).prop('checked', false);
                }

            }
        });
    </script>
@endsection
