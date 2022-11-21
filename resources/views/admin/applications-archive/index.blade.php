@extends('layouts.app')
@push('head-script')
<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
    position: absolute;
    left: auto !important;
}
.ml{
    margin-left: 503px;
}
</style>
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row clearfix">
                        <div class="col-md-12 mb-4 d-flex justify-content-between">
                            <div id="search-container" class="d-flex w-50">
                                <input id="skill" class="form-control mr-2" type="text" name="skill" placeholder="@lang('modules.applicationArchive.enterSkill')">
                                <a href="javascript:;" class="d-none">
                                    <i class="fa fa-times-circle-o"></i>
                                </a>                
                            </div>
                            <button type="button" class="btn btn-sm btn-primary ml deleteButton" id="deleteAllSelectedRecords">delete</button>

                            <a class="pull-right" onclick="exportJobApplication()" >
                                <button class="btn btn-sm btn-primary" type="button">
                                    <i class="fa fa-upload"></i>  @lang('menu.export')
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped ">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox"  id="chkCheckAll" style="position: initial;">
                                </th>
                                <th>#</th>
                                <th>@lang('modules.jobApplication.applicantName')</th>
                                <th>@lang('menu.jobs')</th>
                                <th>@lang('menu.locations')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>

    <script>
        var table;
        loadTable();

        function redrawTable() {
            table._fnDraw();
        }

        function loadTable() {
            table = $('#myTable').dataTable({
                responsive: true,
                // processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{!! route('admin.applications-archive.data') !!}",
                    data: function ( d ) {
                        return $.extend( {}, d, {
                            skill: $('#skill').val()
                        } );
                    }
                },
                language: languageOptions(),
                "fnDrawCallback": function( oSettings ) {
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
                columns: [
                    {data: 'select_orders', name: 'select_orders', orderable: false, searchable: false},
                    { data: 'DT_Row_Index'},
                    { data: 'full_name', name: 'full_name' },
                    { data: 'title', name: 'title', width: '17%'},
                    { data: 'location', name: 'location'},
                ]
            });
            new $.fn.dataTable.FixedHeader( table );
        }
        $(function(e){
    $("#deleteAllSelectedRecords").hide();
            
        $('#chkCheckAll').click(function () {
        var checkboxes =  $('input[name="check[]"]').length;
            $('.checkBoxClass').prop('checked',$(this).prop('checked'));
            if ( checkboxes > 0 && this.checked) {
                $("#deleteAllSelectedRecords" ).show();
            }else{
                $("#deleteAllSelectedRecords").hide();
            }
     });
    $('#deleteAllSelectedRecords').click(function(e){
        e.preventDefault();
       
        var rowdIds = $("#myTable input:checkbox:checked").map(function() {
                return $(this).val();
            }).get();
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {  
                    var url = "{{ route('admin.applications-archive.deleteRecords',':rowdIds') }}";
                    url = url.replace(':rowdIds', rowdIds);

                    var token = "{{ csrf_token() }}"; 
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token },
                        success: function (response) {
                            if (response.status == "success" ) {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
    });
});    
        
        $(function(e){
         var counterChecked = 0;
    $('body').on('change', 'input[name="check[]"]', function() {
    this.checked ? counterChecked++ : counterChecked--;
    counterChecked > 0 ? $('#deleteAllSelectedRecords').show(): $('#deleteAllSelectedRecords').hide();

    });
 });
        function exportJobApplication(){
            var skillVal = $('#skill').val();

            if (skillVal == '') {
                skillVal = undefined
            }

            var url = '{{ route('admin.applications-archive.export', ':skill') }}';
            url = url.replace(':skill', skillVal);

            window.location.href = url;
        }

        search($('#skill'), 500, 'table');

        table.on('click', '.show-detail', function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('row-id');
            var url = "{{ route('admin.applications-archive.show',':id') }}";
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