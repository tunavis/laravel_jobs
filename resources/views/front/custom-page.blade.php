<!-- @extends('layouts.front') -->
<style>
    .header.inner-header{
        height: 274px;
    }
    .header {
        padding: 104px 100px !important;
    }
    @media (max-width:767px) {
        .header.inner-header {
            height: 224px;
        }
    }

</style>
@section('header-text')
    <!-- <h1 class="hidden-sm-down text-white fs-40 mb-10">{{ $pageTitle }}</h1> -->

    <h1 class="hidden-sm-down text-white fs-40 mb-10">{{ $pageTitle }}</h1>
    <h3 class="hidden-sm-up text-white  mb-10">{{ $pageTitle }}</h3>
    <div class="text-white">
        <a class="text-white" href="{{ route('jobs.jobOpenings') }}"><u>@lang('modules.front.jobOpenings')</u>&nbsp; </a> &raquo; &nbsp;<span class="text-white">{{ ucwords($pageTitle) }}</span>
    </div>
@endsection


@section('content')
    <!--
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    | Working at TheThemeio
    |‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
    !-->
    <section class="section bg-gray py-40 min-height-section">
        <div class="container">
            <div class="row gap-y align-items-center">
                <div class="col-12">
                    <h3>@if(!is_null($customPage->name)) {{ $customPage->name }}  @endif</h3>
                    <p>@if(!is_null($customPage->description)) {!! $customPage->description !!}  @endif</p>
                </div>
            </div>
        </div>
    </section>

@endsection