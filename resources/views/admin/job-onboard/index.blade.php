@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">    <link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">

    <style>
        .mb-20{
            margin-bottom: 20px
        }
        .datepicker{
            z-index: 9999 !important;
        }
        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            width: 250px;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('modules.jobApplication.applicantName')</th>
                                <th>@lang('menu.jobs')</th>
                                <th>@lang('menu.locations')</th>
                                <th>@lang('app.joinDate')</th>
                                <th>@lang('app.acceptLastDate')</th>
                                <th>@lang('app.status')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">@lang('menu.cancelReason')</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="save-cancel-reason" method="post">
            <div class="col-xs-12 col-md-12 ">
            <div class="form-group ml-3">
            <label class="required">@lang('app.remark')</label><br>
            <textarea type="text" id="cancel_reason" class="from-control rounded-0" name="cancel_reason" rows="4"></textarea>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary save-cancel-reason">Save</button>
            </div>
        </form>

          </div>
        </div>
      </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>

    <script>

        var table;
        tableLoad('load');

        function tableLoad() {
            table = $('#myTable').dataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                stateSave: true,
                ajax: '{!! route('admin.job-onboard.data') !!}',
                language: languageOptions(),
                "fnDrawCallback": function (oSettings) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    {data: 'id', name:'id'},
                    {data: 'full_name', name: 'job_applications.full_name', width: '17%'},
                    {data: 'title', name: 'jobs.title', width: '17%'},
                    {data: 'location', name: 'job_locations.location'},
                    {data: 'joining_date', name: 'joining_date'},
                    {data: 'accept_last_date', name: 'accept_last_date'},
                    {data: 'hired_status', name: 'hired_status'},
                    {data: 'action', name: 'action', width: '15%', searchable : false}
                ]
            });
            new $.fn.dataTable.FixedHeader(table);
        }

        $('body').on('click', '.send-offer', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.send') @lang('app.offer')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.job-onboard.send-offer',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        container: '#myTable',
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        // Change status in cancel
        $('body').on('click', '.sa-params', function(){
            $('#exampleModal').modal('show'); 
            var id = $(this).data('row-id');
            $("#save-cancel-reason").on("submit",function (e)
            {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    var url = "{{ route('admin.job-onboard.update-status',':id') }}";
                    url = url.replace(':id', id );
                    var token = "{{ csrf_token() }}";
                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        data: formData,
                        container: '#save-cancel-reason',
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                $('#exampleModal').modal('hide'); 
                        }
                    });
                 });
        });

        table.on('click', '.show-detail', function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('row-id');
            var url = "{{ route('admin.job-applications.show',':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        });
    </script>
@endpush