<?php

use App\Http\Controllers\Admin\AdminJobTypeController;
use App\Http\Controllers\Admin\AdminCurrencyController;
use App\Http\Controllers\Admin\AdminWorkExperienceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/zoom-webhook', 'ZoomWebhookController@index')->name('zoom-webhook');
//Front routes start
// Admin routes
Route::group(
    ['namespace' => 'Front', 'as' => 'jobs.'],
    function () {
        Route::get('/', 'FrontJobsController@jobOpenings')->name('jobOpenings')->Middleware('disable-frontend');
        Route::post('/more-data', 'FrontJobsController@moreData')->name('more-data');
        Route::post('/search-job', 'FrontJobsController@searchJob')->name('search-job');
        Route::get('/job-offer/{slug?}', 'FrontJobOfferController@index')->name('job-offer');
        Route::post('/save-offer', 'FrontJobOfferController@saveOffer')->name('save-offer');
        Route::get('/job/{slug}', 'FrontJobsController@jobDetail')->name('jobDetail');
        Route::get('/job/{slug}/apply', 'FrontJobsController@jobApply')->name('jobApply');
        Route::post('/job/saveApplication', 'FrontJobsController@saveApplication')->name('saveApplication');
        Route::post('/job/fetch-country-state', 'FrontJobsController@fetchCountryState')->name('fetchCountryState');
        Route::post('change-language/{code}', 'FrontJobsController@changeLanguage')->name('changeLanguage');
        Route::get('auth/callback/{provider}', 'FrontJobsController@callback')->name('linkedinCallback');
        Route::get('auth/redirect/{provider}', 'FrontJobsController@redirect')->name('linkedinRedirect');
    }
);



//Front routes end


Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::post('mark-notification-read', ['uses' => 'NotificationController@markAllRead'])->name('mark-notification-read');
    Route::post('mark-read', 'NotificationController@markRead')->name('mark_single_notification_read');

    // Admin routes
    Route::group(
        ['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'],
        function () {
            Route::get('/dashboard', 'AdminDashboardController@index')->name('dashboard');

            Route::get('job-categories/data', 'AdminJobCategoryController@data')->name('job-categories.data');
            Route::get('job-categories/getSkills/{categoryId}', 'AdminJobCategoryController@getSkills')->name('job-categories.getSkills');
            Route::resource('job-categories', 'AdminJobCategoryController');

            //Questions
            Route::get('questions/data', 'AdminQuestionController@data')->name('questions.data');
            Route::resource('questions', 'AdminQuestionController');

            Route::post('todo-items/update-todo-item', 'AdminTodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');
            Route::resource('todo-items', 'AdminTodoItemController');

            // company settings
            Route::group(
                ['prefix' => 'settings'],
                function () {
                    // Company Setting routes
                    Route::resource('settings', 'CompanySettingsController', ['only' => ['edit', 'update', 'index']]);

                    // Application Form routes
                    Route::resource('application-setting', 'ApplicationSettingsController');
                    
                    // Role permission routes
                    Route::post('role-permission/assignAllPermission', ['as' => 'role-permission.assignAllPermission', 'uses' => 'ManageRolePermissionController@assignAllPermission']);
                    Route::post('role-permission/removeAllPermission', ['as' => 'role-permission.removeAllPermission', 'uses' => 'ManageRolePermissionController@removeAllPermission']);
                    Route::post('role-permission/assignRole', ['as' => 'role-permission.assignRole', 'uses' => 'ManageRolePermissionController@assignRole']);
                    Route::post('role-permission/detachRole', ['as' => 'role-permission.detachRole', 'uses' => 'ManageRolePermissionController@detachRole']);
                    Route::post('role-permission/storeRole', ['as' => 'role-permission.storeRole', 'uses' => 'ManageRolePermissionController@storeRole']);
                    Route::post('role-permission/deleteRole', ['as' => 'role-permission.deleteRole', 'uses' => 'ManageRolePermissionController@deleteRole']);
                    Route::get('role-permission/showMembers/{id}', ['as' => 'role-permission.showMembers', 'uses' => 'ManageRolePermissionController@showMembers']);
                    Route::resource('role-permission', 'ManageRolePermissionController');

                    //language settings
                    Route::post('language-settings/change-language', ['uses' => 'LanguageSettingsController@changeLanguage'])->name('language-settings.change-language');
                    Route::put('language-settings/change-language-status/{id}', 'LanguageSettingsController@changeStatus')->name('language-settings.changeStatus');

                    Route::resource('language-settings', 'LanguageSettingsController');

                    Route::resource('theme-settings', 'AdminThemeSettingsController');
                    Route::post('theme-settings/disable-frontend', ['uses' => 'AdminThemeSettingsController@disableFrontend'])->name('theme-settings.disableFrontend');

                    Route::get('smtp-settings/sent-test-email', ['uses' => 'AdminSmtpSettingController@sendTestEmail'])->name('smtp-settings.sendTestEmail');
                    Route::resource('smtp-settings', 'AdminSmtpSettingController');

                    Route::resource('sms-settings', 'AdminSmsSettingsController', ['only' => ['index', 'update']]);

                    Route::resource('linkedin-settings', 'AdminLinkedInSettingsController');

                    // Footer Links
                    Route::get('footer-settings/data', 'FooterSettingController@data')->name('footer-settings.data');
                    Route::resource('footer-settings', 'FooterSettingController');

                    Route::get('update-application', ['uses' => 'UpdateApplicationController@index'])->name('update-application.index');
                }
            );
            //zoom
            Route::get('zoom-meeting/table', 'AdminZoomMeetingController@tableView')->name('zoom-meeting.table-view');
            Route::get('zoom-meeting/data', 'AdminZoomMeetingController@data')->name('zoom-meeting.data');
            Route::get('zoom-meeting/start-meeting/{id}', 'AdminZoomMeetingController@startMeeting')->name('zoom-meeting.startMeeting');
            Route::post('zoom-meeting/cancel-meeting', 'AdminZoomMeetingController@cancelMeeting')->name('zoom-meeting.cancelMeeting');
            Route::post('zoom-meeting/end-meeting', 'AdminZoomMeetingController@endMeeting')->name('zoom-meeting.endMeeting');
            Route::post('zoom-meeting/updateOccurrence/{id}', 'AdminZoomMeetingController@updateOccurrence')->name('zoom-meeting.updateOccurrence');
            Route::resource('zoom-meeting', 'AdminZoomMeetingController');
            Route::resource('category', 'CategoryController');
            Route::resource('zoom-setting', 'ZoomMeetingSettingController');
            Route::post('zoom-setting/change-status/{id}', 'ZoomMeetingSettingController@changeStatus')->name('zoom-setting.change-status');
            Route::get('skills/data', 'AdminSkillsController@data')->name('skills.data');
            Route::resource('skills', 'AdminSkillsController');

            Route::get('locations/data', 'AdminLocationsController@data')->name('locations.data');
            Route::resource('locations', 'AdminLocationsController');

            Route::get('jobs/data', 'AdminJobsController@data')->name('jobs.data');

            Route::post('jobs/refresh-date', 'AdminJobsController@refreshDate')->name('jobs.refreshDate');
            
            Route::get('jobs/application-data', 'AdminJobsController@applicationData')->name('jobs.applicationData');
            Route::post('jobs/send-emails', 'AdminJobsController@sendEmails')->name('jobs.sendEmails');
            Route::get('jobs/send-email', 'AdminJobsController@sendEmail')->name('jobs.sendEmail');
            Route::resource('jobs', 'AdminJobsController');

            Route::post('job-applications/rating-save/{id?}', 'AdminJobApplicationController@ratingSave')->name('job-applications.rating-save');
            Route::get('job-applications/create-schedule/{id?}', 'AdminJobApplicationController@createSchedule')->name('job-applications.create-schedule');
            Route::post('job-applications/store-schedule', 'AdminJobApplicationController@storeSchedule')->name('job-applications.store-schedule');
            Route::get('job-applications/question/{jobID}/{applicationId?}', 'AdminJobApplicationController@jobQuestion')->name('job-applications.question');
            Route::get('job-applications/export/{status}/{location}/{startDate}/{endDate}/{jobs}', 'AdminJobApplicationController@export')->name('job-applications.export');
            Route::get('job-applications/data', 'AdminJobApplicationController@data')->name('job-applications.data');
            Route::get('job-applications/table-view', 'AdminJobApplicationController@table')->name('job-applications.table');
            Route::post('job-applications/updateIndex', 'AdminJobApplicationController@updateIndex')->name('job-applications.updateIndex');
            Route::post('job-applications/archive-job-application/{application}', 'AdminJobApplicationController@archiveJobApplication')->name('job-applications.archiveJobApplication');
            Route::post('job-applications/unarchive-job-application/{application}', 'AdminJobApplicationController@unarchiveJobApplication')->name('job-applications.unarchiveJobApplication');
            Route::post('job-applications/add-skills/{applicationId}', 'AdminJobApplicationController@addSkills')->name('job-applications.addSkills');
            Route::resource('job-applications', 'AdminJobApplicationController');

            Route::get('applications-archive/data', 'AdminApplicationArchiveController@data')->name('applications-archive.data');
            Route::get('applications-archive/export/{skill}', 'AdminApplicationArchiveController@export')->name('applications-archive.export');
            Route::resource('applications-archive', 'AdminApplicationArchiveController');
            Route::post('applications-archive{id}', 'AdminApplicationArchiveController@deleteRecords')->name('applications-archive.deleteRecords');
    
            Route::get('job-onboard/data', 'AdminJobOnboardController@data')->name('job-onboard.data');
            Route::get('job-onboard/send-offer/{id?}', 'AdminJobOnboardController@sendOffer')->name('job-onboard.send-offer');
            Route::get('job-onboard/update-status/{id?}', 'AdminJobOnboardController@updateStatus')->name('job-onboard.update-status');
            Route::resource('job-onboard', 'AdminJobOnboardController');

            Route::resource('profile', 'AdminProfileController');
            Route::resource('application-status', 'AdminApplicationStatusController');

            Route::get('interview-schedule/data', 'InterviewScheduleController@data')->name('interview-schedule.data');
            Route::get('interview-schedule/table-view', 'InterviewScheduleController@table')->name('interview-schedule.table-view');
            Route::post('interview-schedule/change-status', 'InterviewScheduleController@changeStatus')->name('interview-schedule.change-status');
            Route::post('interview-schedule/change-status-multiple', 'InterviewScheduleController@changeStatusMultiple')->name('interview-schedule.change-status-multiple');
            Route::get('interview-schedule/notify/{id}/{type}', 'InterviewScheduleController@notify')->name('interview-schedule.notify');
            Route::get('interview-schedule/response/{id}/{type}', 'InterviewScheduleController@employeeResponse')->name('interview-schedule.response');
            Route::resource('interview-schedule', 'InterviewScheduleController');

            Route::get('team/data', 'AdminTeamController@data')->name('team.data');
            Route::post('team/change-role', 'AdminTeamController@changeRole')->name('team.changeRole');
            Route::resource('team', 'AdminTeamController');

            Route::get('company/data', 'AdminCompanyController@data')->name('company.data');
            Route::get('compant/show', 'AdminCompanyController@show')->name('company.show');
            Route::resource('company', 'AdminCompanyController');
            Route::resource('currency-settings', 'AdminCurrencyController');

            Route::resource('storage-settings', 'StorageController');

            Route::resource('applicant-note', 'ApplicantNoteController');

            Route::resource('sticky-note', 'AdminStickyNotesController');

            Route::resource('departments', 'AdminDepartmentController');

            Route::resource('job-type', 'AdminJobTypeController');

            Route::resource('work-experience', 'AdminWorkExperienceController');

            Route::resource('designations', 'AdminDesignationController');

            Route::get('documents/data', 'AdminDocumentController@data')->name('documents.data');
            Route::get('documents/download-document/{document}', 'AdminDocumentController@downloadDoc')->name('documents.downloadDoc');
            Route::resource('documents', 'AdminDocumentController');

            Route::resource('report', 'AdminReportController');

        }
    );

    Route::get('change-mobile', 'VerifyMobileController@changeMobile')->name('changeMobile');
    Route::post('send-otp-code', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode');
    Route::post('send-otp-code/account', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode.account');
    Route::post('verify-otp-phone', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode');
    Route::post('verify-otp-phone/account', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode.account');
    Route::get('remove-session', 'VerifyMobileController@removeSession')->name('removeSession');
});

Route::group(
    ['namespace' => 'Front', 'as' => 'jobs.'],
    function () {
        Route::get('{slug}', 'FrontJobsController@customPage')->name('custom-page');
    }
);
