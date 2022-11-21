<style>
    .hide-box {
        display: none;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title"><i class="icon-plus"></i> @lang('modules.jobs.workExperience')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>@lang('modules.jobs.workExperience')</th>
                <th>@lang('app.action')</th>
            </tr>
            </thead>
            <tbody>
            @forelse($workExperiences as $key=>$workExperience)
                <tr id="dept-{{ $workExperience->id }}">
                    <td>{{ $key+1 }}</td>
                    <td id="deptName{{ $workExperience->id }}">{{ ucwords($workExperience->work_experience) }}</td>
                    <td>
                        <a href="javascript:;" data-dept-id="{{ $workExperience->id }}" class="btn btn-sm btn-danger btn-rounded delete-department"><i class="fa fa-times"></i> </a>
                        <a href="javascript:;" data-dept-id="{{ $workExperience->id }}" class="btn btn-sm btn-info btn-rounded edit-department"><i class="fa fa-pencil"></i> </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">@lang('messages.notFound')</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <hr>
    <form id="createdepartment" class="ajax-form" method="post">
        {{ csrf_field() }}
        <div class="form-body">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="form-group">
                        <label>@lang('modules.jobs.workExperience')</label>
                        <input type="text" name="work_experience" id="work_experience_name" class="form-control" >
                        <input type="hidden" name="edit_id" id="edit_id">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-department" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
            <button type="button" id="update-department" class="btn btn-success hide-box"> <i class="fa fa-check"></i> @lang('app.update')</button>
        </div>


<script>
    // Select 2 init.
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    // Save department
    $('#save-department').click(function () {
        $.easyAjax({
            url: '{{route('admin.work-experience.store')}}',
            container: '#createdepartment',
            type: "POST",
            data: $('#createdepartment').serialize(),
            success: function (response) {
                var options = [];
                    var rData = [];
                    rData = response.data;
                    $.each(rData, function( index, value ) {
                        var selectData = '';
                        selectData = '<option value="'+value.id+'">'+value.work_experience+'</option>';
                        options.push(selectData);
                    });

                    $('#work_experience').html(options);
                    $('#work_experience').select2();
                    $('#addDepartmentModal').modal('hide');
                }
            
        })
    })

    // Update department
    $('#update-department').click(function () {
        var id = $('#edit_id').val();
        var url = "{{ route('admin.work-experience.update',':id') }}";
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}';
        var name = $('#work_experience_name').val();;

        $.easyAjax({
            url: url,
            container: '#createdepartment',
            type: "POST",
            data: {'_token':token, '_method':'PUT','work_experience':name },
            success: function (response) {
                if(response.status == 'success'){
                    var options = [];
                    var rData = [];
                    rData = response.data;
                    $.each(rData, function( index, value ) {
                        var selectData = '';
                        selectData = '<option value="'+value.id+'">'+value.work_experience+'</option>';
                        options.push(selectData);
                    });

                    $('#work_experience').html(options);
                    $('#work_experience').select2();
                    $('#addDepartmentModal').modal('hide');
                    $('#deptName'+id).html(name);// Set name in table row
                    $('#edit_id').val('');// Set edit id field blank
                    $('#name').val('');// Set name field blank
                    $('#update-department').hide();
                    $('#save-department').show();
                }
            }
    });

});

    // Edit department
    $('body').on('click', '.edit-department', function(){
        var id = $(this).data('dept-id');
        $('#edit_id').val(id);// Set id in edit id field
        $('#work_experience_name').val($('#deptName'+id).html());// Set name field by edit name
        $('#update-department').show();
        $('#save-department').hide();
    });
    // Delete Department
    $('body').on('click', '.delete-department', function(){
        var id = $(this).data('dept-id');
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

                var url = "{{ route('admin.work-experience.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#dept-'+id).fadeOut();
                            var options = [];
                            var rData = [];
                            rData = response.data;
                            $.each(rData, function( index, value ) {
                                var selectData = '';
                                selectData = '<option value="'+value.id+'">'+value.name+'</option>';
                                options.push(selectData);
                            });

                            $('#department').html(options);
                            $('#department').select2();
                        }
                    }
                });
            }
        });
    });
</script>
