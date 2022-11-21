@extends('layouts.app')

@if(in_array("add_job_applications", $userPermissions))
@section('create-button')
    <a href="{{ route('admin.job-applications.create') }}" class="btn btn-dark btn-sm m-l-15"><i
                class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
@endsection
@endif

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/lobipanel/dist/css/lobipanel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">

    <style>
        .board-column{
            /* max-width: 21%; */
        }

        .board-column .card{
            box-shadow: none;
        }
        .notify-button{
            /*width: 9em;*/
            height: 1.5em;
            font-size: 0.730rem !important;
            line-height: 0.5 !important;
        }
        .panel-scroll{
            height: calc(100vh - 330px); overflow-y: scroll
        }
        .mb-20{
            margin-bottom: 20px
        }
        .datepicker{
            z-index: 9999 !important;
        }
        .d-block{
            display: block;
        }
        .upcomingdata {
            height: 37.5rem;
            overflow-x: scroll;
        }
        .notify-button{
            height: 1.5em;
            font-size: 0.730rem !important;
            line-height: 0.5 !important;
        }
        .scheduleul
        {
            padding: 0 15px 0 11px;
        }
        .searchInput
        {
            width: 50%; display: inline
        }
        .searchButton
        {
            margin-bottom: 4px;margin-left: 3px;
        }
    </style>
@endpush

@section('content')

    <div class="row mb-2">
        <div class="col-sm-6">
            <a href="javascript:;" id="toggle-filter" class="btn btn-outline btn-success btn-sm toggle-filter">
                <i class="fa fa-sliders"></i> @lang('app.filterResults')
            </a>
            <a href="{{ route('admin.job-applications.table') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-table"></i> @lang('app.tableView')
            </a>
            <a href="#" class="btn btn-sm btn-info mail_setting">
                <i class="fa fa-envelope-o"></i>
                @lang('modules.applicationSetting.mailSettings')
            </a>
            <a href="javascript:createApplicationStatus();" class="btn btn-sm btn-success">
                <i class="fa fa-bookmark-o"></i>
                @lang('modules.jobApplication.newStatus')
            </a>
        </div>
        <div class="col-sm-6">
            <div id="search-container" class="form-group pull-right">
                <input id="search" class="form-control" type="text" name="search" placeholder="@lang('modules.jobApplication.enterName')">
                <a href="javascript:;" class="d-none">
                    <i class="fa fa-times-circle-o"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container-scroll">
        <div class="card" id="ticket-filters">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>@lang('app.filterBy')
                            <a href="javascript:;" class="pull-right mt-2 mr-2 toggle-filter">
                                <i class="fa fa-times-circle-o"></i>
                            </a>
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control" id="date-start" @if($type == '')  value="{{ $startDate }}" @endif name="start_date">
                                <span class="input-group-addon bg-info b-0 text-white p-1">@lang('app.to')</span>
                                <input type="text" class="form-control" id="date-end" name="end_date" @if($type == '')  value="{{ $endDate }}" @endif>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="select2" name="jobs" id="jobs" data-style="form-control">
                                <option value="all">@lang('modules.jobApplication.allJobs')</option>
                                @forelse($jobs as $job)
                                    <option title="{{ucfirst($job->title)}}" value="{{$job->id}}">{{ucfirst($job->title)}}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="select2" name="location" id="location" data-style="form-control">
                                <option value="all">@lang('modules.jobApplication.allLocation')</option>
                                @forelse($locations as $location)
                                    <option value="{{$location->id}}">{{ucfirst($location->location)}}</option>
                                @empty
                                @endforelse

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="select2" name="skill[]" data-placeholder="Select Skills" multiple="multiple" id="skill" data-style="form-control">
                                @forelse($skills as $skill)
                                    <option value="{{$skill->id}}">{{ucfirst($skill->name)}}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="select2" name="question"  id="questions" data-style="form-control">
                                <option value="all">@lang('modules.jobApplication.allQuestion')</option>
                                @forelse($questions as $question)
                                    <option value="{{$question->id}}">{{ucfirst($question->question)}}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6" id="question_value">
                        <div class="form-group">
                            <input type="text" class="form-control" name="question_value" id="question-value" placeholder="Enter question value">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="button" id="apply-filters" class="btn btn-success btn-sm"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            <button type="button" id="reset-filters" class="btn btn-info btn-sm"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row container-row">
        </div>
    </div>
    @include('admin.application-setting.modal')
    {{--Ajax Modal Start for--}}
    <div class="modal fade bs-modal-md in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
    <script src="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/lobipanel/dist/js/lobipanel.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jquery-bar-rating-master/dist/jquery.barrating.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <script>
        $(".select2").select2({
            width: '100%'
        });
        loadData();

        $('#apply-filters').click(function () {
            loadData();
        });

        $('#reset-filters').click(function () {
            $('#date-start').val('{{ $startDate }}');
            $('#date-end').val('{{ $endDate }}');
            $('#jobs').val('all').trigger('change');

            loadData();
        })
        $('#applySearch').click(function () {
            var search = $('#search').val();
            if(search !== undefined && search !== null && search !== ""){
                loadData();
            }
        })

        $('#date-end').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
        $('#date-start').bootstrapMaterialDatePicker({ weekStart : 0, time: false }).on('change', function(e, date)
        {
            $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
        });

        // Schedule create modal view
        function createSchedule (id) {
            var url = "{{ route('admin.job-applications.create-schedule',':id') }}";
            url = url.replace(':id', id);
            $('#modelHeading').html('Schedule');
            $.ajaxModal('#scheduleDetailModal', url);
        }

        // Create application status modal view
        function createApplicationStatus () {
            var url = "{{ route('admin.application-status.create') }}";

            $('#modelHeading').html('Application Status');
            $.ajaxModal('#scheduleDetailModal', url);
        }

        function deleteStatus(id) {
            const panels = $('.board-column[data-column-id="' + id + '"').find('.lobipanel.show-detail');
            let applicationIds = [];
            panels.each((ind, element) => {
                applicationIds.push($(element).data('application-id'));
            });

            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteStatusWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {
                    let url = "{{ route('admin.application-status.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    let data = {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE',
                        applicationIds: applicationIds
                    }

                    $.easyAjax({
                        url,
                        data,
                        type: 'POST',
                        container: '.container-row',
                        success: function (response) {
                            if (response.status == 'success') {
                                loadData();
                            }
                        }
                    })
                }
            });
        }

        function editStatus(id) {
            var url = "{{ route('admin.application-status.edit', ':id') }}";
            url = url.replace(':id', id);

            $('#modelHeading').html('Application Status');
            $.ajaxModal('#scheduleDetailModal', url);
        }

        function saveStatus() {
            $.easyAjax({
                url: "{{ route('admin.application-status.store') }}",
                container: '#createStatus',
                type: "POST",
                data: $('#createStatus').serialize(),
                success: function (response) {
                    $('#scheduleDetailModal').modal('hide');
                    loadData();
                }
            });
        }

        function updateStatus(id) {
            let url = "{{ route('admin.application-status.update', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#updateStatus',
                type: "POST",
                data: $('#updateStatus').serialize(),
                success: function (response) {
                    $('#scheduleDetailModal').modal('hide');
                    loadData();
                }
            });
        }

        function loadData () {
            var startDate = $('#date-start').val();
            var jobs = $('#jobs').val();
            var location = $('#location').val();
            var skill = $('#skill').val();
            var questions = $('#questions').val();
            var search = $('#search').val();
            var question_value = $('#question-value').val();

            if (startDate == '') {
                startDate = null;
            }

            var endDate = $('#date-end').val();

            if (endDate == '') {
                endDate = null;
            }

            var url = '{{route('admin.job-applications.index')}}?startDate=' + startDate + '&endDate=' + endDate + '&jobs=' + jobs + '&search=' + search + '&location=' + location+ '&skill=' + skill + '&questions=' + questions +'&question_value=' +question_value;

            $.easyAjax({
                url: url,
                container: '.container-row',
                type: "GET",
                success: function (response) {
                    $('.container-row').html(response.view);
                }

            })
        }

        search($('#search'), 500, 'data');

        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        });

        $('#question_value').hide();
        $('#questions').change(function(){
            $('#question_value').show();
        });

         //click mail setting open modal
         $(document).on('click','.mail_setting',function(){
            var data1 = '';
            $.ajax({
                url: "{{route('admin.application-setting.create')}}",
                success: function(data){
                    data1 = eval(data.mail_setting);
                    var options = '';
                    $.each(data1, function(name,status){       
                        if(status.status == true){               
                        options += '<input type="checkbox"  checked style=" style="text-align: center; margin: 6px 15px 13px 0px;" name="checkBoardColumn[]" id="checkbox-' + name + '" value="' + name+ '"  />';
                        options += '<label for="checkbox-' + name + '" style="text-align: center; margin: 6px 15px 13px 0px;">' +status.name+ '</label>';
                         }else{
                            options += '<input type="checkbox" style="text-align: center; margin: 6px 10px 4px 0px;" class = "iCheck-helper" name="checkBoardColumn[]" id="checkbox-' + name + '" value="' + name+ '"  />';
                            options += '<label for="checkbox-' + name + '" style="text-align: center; margin: 6px 10px 4px 0px;">' +status.name+ '</label>';
                         }
                        });
                        $('#assetNameMenu').html(options);
                    $('#legal_term').val(data.legal_term);
                    $('#ModalLoginForm').modal('show');
                    return false;
              }
              
              });
            
        });

    </script>
@endpush
