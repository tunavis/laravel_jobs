@extends('layouts.app')

@push('head-script')
<style>
    .field-icon {
    float: right;
    cursor: pointer;
    margin-left: -25px;
    margin-top: -25px;
    position: relative;
    z-index: 2;
    margin-right: 7px;
}
    </style>
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush

@section('content')
    {!!  $smtpSetting->set_smtp_message !!}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">@lang('app.createNew')</h4>

                    <form id="editSettings" class="ajax-form">
                        @csrf

                        <div class="form-group">
                            <label for="name" class="required">@lang('app.name')</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="email" class="required">@lang('app.email')</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password" class="required">@lang('app.password')</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <span class="fa fa-fw fa-eye-slash field-icon " onclick="showPassword()"></span>
                            <span class="help-block"> @lang('messages.passwordlength')</span>
                        </div>
                        <div class="form-group">
                            <label class="required">@lang('app.mobile')</label>
                            <div class="form-row">
                                <div class="col-sm-3">
                                    <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%" >
                                        @foreach ($calling_codes as $code => $value)
                                            <option value="{{ $value['dial_code'] }}">{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="mobile">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('app.image')</label>
                            <div class="card">
                                <div class="card-body">
                                    <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg"
                                           class="dropify"
                                           data-default-file="{{ asset('avatar.png')  }}"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_phone">@lang('modules.permission.roleName')</label>
                            <select class="form-control" name="role_id" id="role_id">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
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

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.team.store')}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
        $(function(){
            // $(this).addClass('fa-eye');
        $('.field-icon').click(function(){
            if($(this).hasClass('fa-eye-slash')){
                $(this).removeClass('fa-eye-slash');
                $(this).addClass('fa-eye');
                $('#password').attr('type','text');
            }else{
                $(this).removeClass('fa-eye');
                $(this).addClass('fa-eye-slash');  
                $('#password').attr('type','password');
                }
            });
        });
    </script>

@endpush