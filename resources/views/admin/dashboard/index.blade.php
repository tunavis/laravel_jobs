@extends('layouts.app')

@push('head-script')
<style>
    .ml{
        margin-left: 130px;
    }
</style>
    <link rel="stylesheet" href="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.css') }}">
@endpush

@section('content')
    @if($global->system_update == 1)
        @php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
        @if(isset($updateVersionInfo['lastVersion']))
            <div class="row alert alert-info">

                <div class="col-md-10">
                    <i class="ti-gift"></i> @lang('modules.update.newUpdate')
                    <label class="label label-success"></label>
                </div>
                <div class="col-md-2 pull-right">
                    <a style="text-decoration: none" href="{{route('admin.update-application.index')}}"
                       class="btn btn-success btn-sm ml">Update Now
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>
         @endif
    @endif 
    @if (!$user->mobile_verified && $smsSettings->nexmo_status == 'active')
        <div id="verify-mobile-info" class="col-md-12">
            <div class="alert alert-info col-md-12" role="alert">
                <div class="row">
                    <div class="col-md-10 d-flex align-items-center">
                        <i class="fa fa-info fa-3x mr-2"></i>
                        @lang('messages.info.verifyAlert')
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-end">
                        <a href="{{ route('admin.profile.index') }}" class="btn btn-warning">
                            @lang('menu.profile')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(!$progress['progress_completed'])
        @include('admin.dashboard.get_started')
    @endif
    <div class="row ">

        <div class="col">
            <div class="row">
                <!-- Column -->
                <div class="col-md-6">
        
                        @if($user->cans('view_company'))
                    
                    <a href="{{ route('admin.company.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-dark text-center rounded">
                                <h1 class="font-light text-white">{{ $totalCompanies }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.totalCompanies')</h6>
                            </div>
                        </div>
                    </a>
                    
                    @else
                    <div class="card">
                        <div class="box bg-dark text-center rounded">
                            <h1 class="font-light text-white">{{ $totalCompanies }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.totalCompanies')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_jobs'))
                    <a href="{{ route('admin.jobs.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-info text-center rounded">
                                <h1 class="font-light text-white">{{ $totalOpenings }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.totalOpenings')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-info text-center rounded">
                            <h1 class="font-light text-white">{{ $totalOpenings }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.totalOpenings')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_job_applications'))
                    <a href="{{ route('admin.job-applications.index') }}?type=dash" target="_blank">
                        <div class="card">
                            <div class="box bg-primary text-center rounded">
                                <h1 class="font-light text-white">{{ $totalApplications }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.totalApplications')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-primary text-center rounded">
                            <h1 class="font-light text-white">{{ $totalApplications }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.totalApplications')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_job_applications'))

                    <a href="{{ route('admin.job-applications.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-success text-center rounded">
                                <h1 class="font-light text-white">{{ $totalHired }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.totalHired')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-success text-center rounded">
                            <h1 class="font-light text-white">{{ $totalHired }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.totalHired')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_job_applications'))

                    <a href="{{ route('admin.job-applications.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-dark text-center rounded">
                                <h1 class="font-light text-white">{{ $totalRejected }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.totalRejected')</h6>
                            </div>
                        </div>
                    </a>
                    @else

                    <div class="card">
                        <div class="box bg-dark text-center rounded">
                            <h1 class="font-light text-white">{{ $totalRejected }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.totalRejected')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_job_applications'))

                    <a href="{{ route('admin.job-applications.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-danger text-center rounded">
                                <h1 class="font-light text-white">{{ $newApplications }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.newApplications')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-danger text-center rounded">
                            <h1 class="font-light text-white">{{ $newApplications }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.newApplications')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_job_applications'))

                    <a href="{{ route('admin.job-applications.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-warning text-center rounded">
                                <h1 class="font-light text-white">{{ $shortlisted }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.shortlistedCandidates')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-warning text-center rounded">
                            <h1 class="font-light text-white">{{ $shortlisted }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.shortlistedCandidates')</h6>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Column -->
                <div class="col-md-6">
                    @if($user->cans('view_schedule'))
                    <a href="{{ route('admin.interview-schedule.index') }}" target="_blank">
                        <div class="card">
                            <div class="box bg-primary text-center rounded">
                                <h1 class="font-light text-white">{{ $totalTodayInterview }}</h1>
                                <h6 class="text-white">@lang('modules.dashboard.todayInterview')</h6>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card">
                        <div class="box bg-primary text-center rounded">
                            <h1 class="font-light text-white">{{ $totalTodayInterview }}</h1>
                            <h6 class="text-white">@lang('modules.dashboard.todayInterview')</h6>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @if ($user->roles->count() > 0)
            <div class="col">
                <div class="row">
                    <div class="col-md-12" id="todo-items-list">

                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="modal fade bs-modal-md in" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModal"
         aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Do you like Recruit? Help us Grow :)</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="note note-info font-14 m-l-5">

                        We hope you love it. If you do, would you mind taking 10 seconds to leave me a short review on codecanyon?
                        <br>
                        This helps us to continue providing great products, and helps potential buyers to make confident decisions.
                        <hr>
                        Thank you in advance for your review and for being a preferred customer.

                        <hr>

                        <p class="text-center">
                            <a href="{{\Froiden\Envato\Functions\EnvatoUpdate::reviewUrl()}}"> <img src="{{asset('assets/images/recruit-review.png')}}" alt=""></a>
                            <button type="button" class="btn btn-link" data-dismiss="modal" onclick="hideReviewModal('closed_permanently_button_pressed')">Hide Pop up permanently</button>
                            <button type="button" class="btn btn-link" data-dismiss="modal" onclick="hideReviewModal('already_reviewed_button_pressed')">Already Reviewed</button>
                        </p>

                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{\Froiden\Envato\Functions\EnvatoUpdate::reviewUrl()}}" target="_blank" type="button" class="btn btn-success">Give Review</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>

    <script>
        var updated = true;

        function showNewTodoForm() {
            let url = "{{ route('admin.todo-items.create') }}"

            $.ajaxModal('#application-md-modal', url);
        }

        function initSortable() {
            let updates = {'pending-tasks': false, 'completed-tasks': false};
            let completedFirstPosition = $('#completed-tasks').find('li.draggable').first().data('position');
            let pendingFirstPosition = $('#pending-tasks').find('li.draggable').first().data('position');

            $('#pending-tasks').sortable({
                connectWith: '#completed-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function (event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks']===true && updates['completed-tasks']===true)
                    {
                        const inverseIndex =  completedFirstPosition > 0 ? completedFirstPosition - ui.item.index() + 1 : 1;
                        const newPosition = inverseIndex;

                        updateTodoItem(id, position={oldPosition, newPosition}, status='completed');

                    }
                    else if(updates['pending-tasks']===true && updates['completed-tasks']===false)
                    {
                        const newPosition = pendingFirstPosition - ui.item.index();

                        updateTodoItem(id, position={oldPosition, newPosition});
                    }

                    //finally, clear out the updates object
                    updates['pending-tasks']=false;
                    updates['completed-tasks']=false;
                },
                update: function (event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();

            $('#completed-tasks').sortable({
                connectWith: '#pending-tasks',
                cursor: 'move',
                handle: '.handle',
                stop: function (event, ui) {
                    const id = ui.item.data('id');
                    const oldPosition = ui.item.data('position');

                    if (updates['pending-tasks']===true && updates['completed-tasks']===true)
                    {
                        const inverseIndex =  pendingFirstPosition > 0 ? pendingFirstPosition - ui.item.index() + 1 : 1;
                        const newPosition = inverseIndex;

                        updateTodoItem(id, position={oldPosition, newPosition}, status='pending');
                    }
                    else if(updates['pending-tasks']===false && updates['completed-tasks']===true)
                    {
                        const newPosition = completedFirstPosition - ui.item.index();

                        updateTodoItem(id, position={oldPosition, newPosition});
                    }

                    //finally, clear out the updates object
                    updates['pending-tasks']=false;
                    updates['completed-tasks']=false;
                },
                update: function (event, ui) {
                    updates[$(this).attr('id')] = true;
                }
            }).disableSelection();
        }

        function updateTodoItem(id, pos, status=null) {
            let data = {
                _token: '{{ csrf_token() }}',
                id: id,
                position: pos,
            };

            if (status) {
                data = {...data, status: status}
            }

            $.easyAjax({
                url: "{{ route('admin.todo-items.updateTodoItem') }}",
                type: 'POST',
                data: data,
                container: '#todo-items-list',
                success: function (response) {
                    $('#todo-items-list').html(response.view);
                    initSortable();
                }
            });
        }

        function showUpdateTodoForm(id) {
            let url = "{{ route('admin.todo-items.edit', ':id') }}"
            url = url.replace(':id', id);

            $.ajaxModal('#application-md-modal', url);
        }

        function deleteTodoItem(id) {
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
                    let url = "{{ route('admin.todo-items.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    let data = {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    }

                    $.easyAjax({
                        url,
                        data,
                        type: 'POST',
                        container: '#roleMemberTable',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#todo-items-list').html(response.view);
                                initSortable();
                            }
                        }
                    })
                }
            });
        }

        @if ($user->roles->count() > 0)
            $('#todo-items-list').html(`{!! $todoItemsView !!}`);
        @endif

        initSortable();

        $('body').on('click', '#create-todo-item', function () {
            $.easyAjax({
                url: "{{route('admin.todo-items.store')}}",
                container: '#createTodoItem',
                type: "POST",
                data: $('#createTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $('#todo-items-list').html(response.view);
                        initSortable();

                        $('#application-md-modal').modal('hide');
                    }
                }
            })
        });

        $('body').on('click', '#update-todo-item', function () {
            const id = $(this).data('id');
            let url = "{{route('admin.todo-items.update', ':id')}}"
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#editTodoItem',
                type: "POST",
                data: $('#editTodoItem').serialize(),
                success: function (response) {
                    if(response.status == 'success'){
                        $('#todo-items-list').html(response.view);
                        initSortable();

                        $('#application-md-modal').modal('hide');
                    }
                }
            })
        });

        $('body').on('change', '#todo-items-list input[name="status"]', function () {
            const id = $(this).data('id');
            let status = 'pending';

            if ($(this).is(':checked')) {
                status = 'completed';
            }

            updateTodoItem(id, null, status);
        })
    </script>
    <script>
        @if(\Froiden\Envato\Functions\EnvatoUpdate::showReview())
        $(document).ready(function () {
            $('#reviewModal').modal('show');
        })
        function hideReviewModal(type) {
            var url = "{{ route('hide-review-modal',':type') }}";
            url = url.replace(':type', type);

            $.easyAjax({
                url: url,
                type: "GET",
                container: "#reviewModal",
            });
        }
        @endif
    </script>
@endpush
