
@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 ">
                            <form id="StorageSetting" class="ajax-form" method="POST">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
    
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label class="control-label">@lang('menu.selectStorage')</label>
                                                <select class="select2 form-control" id="storage"
                                                    name="storage">
                                                    <option value="local" @if(isset($local) &&
                                                        $local->status == 'enabled') selected
                                                        @endif>@lang('menu.local')</option>
                                                    <option value="aws" @if(isset($S3data) &&
                                                        $S3data->status == 'enabled') selected
                                                        @endif>@lang('menu.aws')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 aws-form">
                                            <div class="form-group">
                                                <label>AWS Key</label>
                                                <input type="text" class="form-control" name="aws_key"
                                                    @if(isset($S3data->key))
                                                value="{{ $S3data->key }}" @endif >
                                            </div>
                                            <div class="form-group">
                                                <label>AWS Secret</label>
                                                <input type="password" class="form-control" name="aws_secret"
                                                    @if(isset($S3data->secret))
                                                value="{{ $S3data->secret }}" @endif>
                                            </div>
                                            <div class="form-group">
                                                <label>AWS Region</label>
                                                <select class="select2 form-control" name="aws_region">
                                                    @foreach (\App\StorageSetting::$awsRegions as $key =>
                                                    $region)
    
                                                    <option @if(isset($S3data) && $S3data->
                                                        region == $key) selected @endif
                                                        value="{{$key}}">{{  $region }}</option>
    
                                                    @endforeach
    
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>AWS Bucket</label>
                                                <input type="text" class="form-control" name="aws_bucket"
                                                    @if(isset($S3data->bucket))
                                                value="{{ $S3data->bucket }}" @endif>
                                            </div>
                                        </div>
    
                                    </div>
    
                                    <!--/row-->
    
                                </div>
                                <div class="form-actions">
                                    <button type="button" id="save-form" class="btn btn-success"><i
                                            class="fa fa-check"></i>
                                        @lang('app.save')
                                    </button>
    
                                    <button type="button" id="test-aws" class="aws-form btn btn-primary">Test
                                        AWS S3</button>
    
    
                                </div>
                            </form>
                    </div>
                </div>



                </div>
            </div>
        </div>
    </div>
    @endsection

@push('footer-script')
@push('footer-script')


<script>
    
            $(function () {
                @if ($local->status == "disabled")
                    $('.aws-form').show();
                @else  
                     $('.aws-form').hide();
                @endif
            $('#storage').on('change', function(event) {
                event.preventDefault();
                var type = $(this).val();
                if (type == 'aws') {
                    $('.aws-form').show();
                } else if(type == 'local') {
                    $('.aws-form').hide();
                }
            });
            });
            
            $('#save-form').click(function () {
                // alert('wffe');
            $.easyAjax({
                url: '{{ route('admin.storage-settings.store')}}',
                container: '#StorageSetting',
                type: "POST",
                redirect: true,
                data: $('#StorageSetting').serialize(),
                success: function(response){
                    if(response.status == 'success' && response.storage =='aws') {
                        // $('.aws-form').show();
                    }
                    
                 }
            })
        });

</script>
@endpush
