@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="verify-mobile">
                        @include('sections.admin_verify_phone')
                    </div>
                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name" class="required">@lang('app.name')</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ ucwords($user->name) }}">
                        </div>
                        <div class="form-group">
                            <label for="email" class="required">@lang('app.email')</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ $user->email }}">
                        </div>
                        <div class="form-group">
                            <label for="password">@lang('app.password')</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="@lang('passwords.passwordLength')">
                            <span class="help-block"> @lang('messages.passwordNote')</span>
                        </div>
                        @if ($smsSettings->nexmo_status == 'deactive')
                            <!-- text input -->
                            <div class="form-group">
                                <label class="required">@lang('app.mobile')</label>
                                <div class="form-row">
                                    <div class="col-sm-3">
                                        <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%" >
                                            @foreach ($calling_codes as $code => $value)
                                                <option value="{{ $value['dial_code'] }}"
                                                @if ($user->calling_code)
                                                    {{ $user->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                                @endif>{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="mobile" value="{{ $user->mobile }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('app.image')</label>
                            <div class="card">
                                <div class="card-body">
                                    <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify"
                                           data-default-file="{{ $user->profile_image_url  }}"
                                    />
                                </div>
                            <span class="help-block"> @lang('messages.imageNote')</span>

                            </div>
                        </div>


                        <button type="button" id="save-form"
                                class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                        <button type="reset"
                                class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('body').on('click', '#change-mobile', function () {
            $.easyAjax({
                url: "{{ route('changeMobile') }}",
                container: '#verify-mobile',
                type: "GET",
                success: function (response) {
                    $('#verify-mobile').html(response.view);
                    $('.selectpicker').selectpicker({
                        style: 'btn-info',
                        size: 4
                    });
                }
            })
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.profile.update', $user->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })

        });
    </script>
    <script>
        var x = '';
        
        function clearLocalStorage() {
            localStorage.removeItem('otp_expiry');
            localStorage.removeItem('otp_attempts');
        }

        function checkSessionAndRemove() {
            $.easyAjax({
                url: '{{ route('removeSession') }}',
                type: 'GET',
                data: {'sessions': ['verify:request_id']}
            })
        }

        function startCounter(deadline) {
            x = setInterval(function() {
                var now = new Date().getTime();
                var t = deadline - now;

                var days = Math.floor(t / (1000 * 60 * 60 * 24));
                var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
                var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((t % (1000 * 60)) / 1000);

                $('#demo').html('Time Left: '+minutes + ":" + seconds);
                $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);

                if (t <= 0) {
                    clearInterval(x);
                    clearLocalStorage();
                    checkSessionAndRemove();
                    location.href = '{{ route('admin.profile.index') }}'
                }
            }, 1000);
        }

        if (localStorage.getItem('otp_expiry') !== null) {
            let localExpiryTime = localStorage.getItem('otp_expiry');
            let now = new Date().getTime();

            if (localExpiryTime - now < 0) {
                clearLocalStorage();
                checkSessionAndRemove();
            }
            else {
                $('#otp').focus().select();
                startCounter(localStorage.getItem('otp_expiry'));
            }
        }

        function sendOTPRequest() {
            $.easyAjax({
                url: '{{ route('sendOtpCode.account') }}',
                type: 'POST',
                container: '#request-otp-form',
                messagePosition: 'inline',
                data: $('#request-otp-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        localStorage.setItem('otp_attempts', 3);

                        $('#verify-mobile').html(response.view);
                        $('.attempts_left').html(`3 attempts left`);

                        let html = `<div class="alert alert-info mb-0" role="alert">
                            @lang('messages.info.verifyAlert')
                            <a href="{{ route('admin.profile.index') }}" class="btn btn-warning">
                                @lang('menu.profile')
                            </a>
                        </div>`;

                        $('#verify-mobile-info').html(html);
                        $('#otp').focus();

                        var now = new Date().getTime();
                        var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}')*1000).getTime();

                        localStorage.setItem('otp_expiry', deadline);
                        // intialize countdown
                        startCounter(deadline);
                    }
                    if (response.status == 'fail') {
                        $('#mobile').focus();
                    }
                }
            });
        }

        function sendVerifyRequest() {
            $.easyAjax({
                url: '{{ route('verifyOtpCode.account') }}',
                type: 'POST',
                container: '#verify-otp-form',
                messagePosition: 'inline',
                data: $('#verify-otp-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        clearLocalStorage();

                        $('#verify-mobile').html(response.view);
                        $('#verify-mobile-info').html('');

                        // select2 reinitialize
                        $('.selectpicker').selectpicker({
                            style: 'btn-info',
                            size: 4
                        });
                    }
                    if (response.status == 'fail') {
                        // show number of attempts left
                        let currentAttempts = localStorage.getItem('otp_attempts');

                        if (currentAttempts == 1) {
                            clearLocalStorage();
                        }
                        else {
                            currentAttempts -= 1;

                            $('.attempts_left').html(`${currentAttempts} attempts left`);
                            $('#otp').focus().select();
                            localStorage.setItem('otp_attempts', currentAttempts);
                        }

                        if (Object.keys(response.data).length > 0) {
                            $('#verify-mobile').html(response.data.view);

                            // select2 reinitialize
                            $('.selectpicker').selectpicker({
                                style: 'btn-info',
                                size: 4
                            });

                            clearInterval(x);
                        }
                    }
                }
            });
        }

        $('body').on('submit', '#request-otp-form', function (e) {
            e.preventDefault();
            sendOTPRequest();
        })

        $('body').on('click', '#request-otp', function () {
            sendOTPRequest();
        })

        $('body').on('submit', '#verify-otp-form', function (e) {
            e.preventDefault();
            sendVerifyRequest();
        })

        $('body').on('click', '#verify-otp', function() {
            sendVerifyRequest();
        })
    </script>
@endpush