<form method="POST" class="ajax-form" id="request-otp-form">
    @csrf
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <label for="mobile">@lang('app.mobile')</label>
            </div>
            <div class="col-md-10">
                <div class="form-row">
                    <div class="col-sm-2">
                        <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" >
                            @foreach ($calling_codes as $code => $value)
                                <option value="{{ $value['dial_code'] }}"
                                @if ($user->calling_code)
                                    {{ $user->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                @endif>{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $user->mobile }}" autofocus />
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" id="request-otp" class="btn btn-primary w-100">@lang('app.requestOTP')</button>
            </div>
        </div>
    </div>
</form>