<a href="javascript:;" data-link="{{ route('admin.job-applications.index') }}" class="dropdown-item text-sm read-notification" data-notification-id="{{ $notification->id }}">
    <i class="fa fa-users mr-2"></i>
    <span class="text-truncate-notify" style="overflow-y: hidden" title="full name">{{ __('messages.notifications.scheduleStatusCandidate', ['name' => $notification->data['data']['full_name']]) }}</span>
    <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
    <div class="clearfix"></div>
</a>