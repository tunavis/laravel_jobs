@extends('layouts.front')

@section('header-text')
    <h1 class="hidden-sm-down text-white fs-50 mb-30">@if(!is_null($frontTheme->welcome_title)) {{ $frontTheme->welcome_title }} @else @lang('modules.front.homeHeader')@endif </h1>
    <h4 class="hidden-sm-up text-white mb-30"> @if(!is_null($frontTheme->welcome_title)) {{ $frontTheme->welcome_title }} @else @lang('modules.front.homeHeader')@endif </h4>
    <p class="text-white mb-40">@if(!is_null($frontTheme->welcome_sub_title)) {!! $frontTheme->welcome_sub_title !!}  @else @lang('modules.front.jobOpeningText') @endif</p>
    <div class="location-search d-flex rounded-pill bg-white ">

        <div class="align-items-center d-flex rounded-pill location height-50">
            <select class="myselect" name="loaction" id ="location_id">
                <option value="all">@lang('modules.front.allLocation')</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ ucfirst($location->location) }}</option>
                    @endforeach
            </select>
        </div>

        <span class="space position-relative hidden-sm-down "></span>

        <div class="align-items-center d-flex rounded-pill designation height-50">
            <select class="myselect" name="category" id ="category">
                <option value="all">@lang('modules.front.allCategory')</option>
                @foreach($categories as $category)
                     <option value="{{ $category->id }}">{{ ucfirst($category->name) }}</option>
                @endforeach
            </select>
        </div>

        <div class="search-btn w-25 rounded-pill align-items-center ">
            <button type="button" name="search" class="btn btn-lg btn-dark height-48 mr-4 my-1 align-items-center d-flex rounded-pill justify-content-center"  id="search">SEARCH</button>

            {{-- <a class="btn btn-lg btn-dark height-48 mr-4 my-1 align-items-center d-flex rounded-pill justify-content-center" href="#">SEARCH</a> --}}
        </div>
    </div>
@endsection

@section('content')



    <!--
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    | Working at TheThemeio
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    !-->
    <!-- <section class="section bg-gray py-60 aaaaa">
        <div class="container">

            <div class="row gap-y align-items-center">

                <div class="col-12">
                    <h3>@if(!is_null($frontTheme->welcome_title)) {{ $frontTheme->welcome_title }} @else @lang('modules.front.jobOpeningHeading') @endif</h3>
                    <p>@if(!is_null($frontTheme->welcome_sub_title)) {!! $frontTheme->welcome_sub_title !!}  @else @lang('modules.front.jobOpeningText') @endif</p>

                </div>

            </div>

        </div>
    </section> -->





    <!--
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    | Open positions
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    !-->
    <section class="section">
        <div class="container">
            <header class="section-header">
                <h2>@lang('modules.front.jobOpenings')</h2>
                <hr>
                <hr>
            </header>


            <div data-provide="shuffle" id="applicant-notes">

               

                <div class="row gap-y" id="jobList" >

                    @forelse($jobs as $job)
                        <div class="col-12 col-md-6 col-lg-4 portfolio-2 job-list" data-shuffle="item" data-groups="{{ $job->location->location.','.$job->category->name }}">
                            <a href="{{ route('jobs.jobDetail', [$job->slug]) }}" class="job-opening-card">
                            <div class="card card-bordered">
                                <div class="card-block">

                                    <h5 class="card-title mb-0">{{ ucwords($job->title) }}</h5>
                                    @if($job->company->show_in_frontend == 'true')
                                    @if($job->job_company_id != null && $job->job_company_id != '')
                                        <small class="company-title mb-50">@lang('app.by') {{ ucwords($job->jobCompany->company_name) }}</small>
                                    @else
                                        <small class="company-title mb-50">@lang('app.by') {{ ucwords($job->company->company_name) }}</small>
                                    @endif
                                        {{-- <small class="company-title mb-50">@lang('app.by') {{ ucwords($job->company->company_name) }}</small> --}}
                                    @endif
                                    <div class="d-flex flex-wrap justify-content-between card-location">
                                        <span class="fw-400 fs-14"><i class="mr-5 fa fa-map-marker"></i>{{ ucwords($job->location->location).', '.ucwords($job->location->country->country_name) }}</span>
                                        <span class="fw-400 fs-14">{{ ucwords($job->category->name) }}<i class="ml-5 fa fa-graduation-cap"></i></span>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    @empty
                        <h4 id ="no-data" class="mx-auto mt-50 mb-40 card-title mb-0" >@lang('modules.front.noData') </h4>
                    @endforelse

                </div>

                <div class="row gap-y">
                    <button type="button" name="load_more_button" class="btn btn-lg btn-white mx-auto mt-50 mb-40"  id="load_more_button">@lang('modules.front.loadMore')</button>
                </div>

            </div>


        </div>
    </section>

@endsection
@push('footer-script')
<script>
    $(document).ready(function(){
        var perPage = '{{ $perPage }}';

        totalCurrentData = perPage;
        var jobCount = {{ $jobCount }};
        if(jobCount > perPage)
        {
            $('#load_more_button').show();
        }
        else{
            $('#load_more_button').hide();
        }
        var totalCurrentCount = $(".job-list").length;

        //load more
        $('body').on('click', '#load_more_button', function () {
            var location_id = $('#location_id').val();
            var category = $('#category').val();
            var token = '{{ csrf_token() }}';
            $('#load_more_button').html('<b>'+"@lang('app.loading')"+'...</b>');
            $.easyAjax({
                url:"{{ route('jobs.more-data') }}",
                type:'POST',
                data: {'_token':token, 'totalCurrentData':totalCurrentData,'location_id':location_id, 'category':category },
                success:function(response) {
                    $('#jobList').append(response.view);
                    totalCurrentData = response.data.job_current_count;
                    $('#load_more_button').blur();
                    $('#load_more_button').html('@lang('modules.front.loadMore')');
                    if (response.data.hideButton !== 'undefined' && response.data.hideButton === 'yes'){
                        $('#load_more_button').hide();
                    }
                    if (response.data.hideButton !== 'undefined' && response.data.hideButton === 'no') {
                        $('#load_more_button').show();
                    }
                }
            });
        });

        //search
        $('body').on('click', '#search', function () {
            var location_id = $('#location_id').val();
            var category = $('#category').val();
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                url:"{{ route('jobs.search-job') }}",
                type:'POST',
                data: {'_token':token, location_id:location_id, category:category},
                success:function(response){
                    $('#jobList').html(response.view);
                    totalCurrentData = response.data.job_current_count;
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#applicant-notes").offset().top
                    }, 2000);
                    if (response.data.hideButton !== 'undefined' && response.data.hideButton === 'yes'){
                        $('#load_more_button').hide();
                    }
                    if (response.data.hideButton !== 'undefined' && response.data.hideButton === 'no') {
                        $('#load_more_button').show();
                    }
                }
            });
        });
    });

    </script>
    @endpush