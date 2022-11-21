<style>
     .required:after {
    content:" *";
    color: red;
  }
</style>
@if (!is_null($job->required_columns))    
    @if ($job->required_columns['gender'])
        <label class="control-label required">@lang('modules.front.gender')</label>
        <div class="form-group">
            <div class="form-inline">
                @foreach ($gender as $key => $value)
                    <div class="form-check form-check-inline">
                        <input @if (!empty($application) && $key == $application->gender) checked @endif class="form-check-input" type="radio" name="gender" id="{{ $key }}" value="{{ $key }}">
                        <label class="form-check-label" for="{{ $key }}">{{ ucFirst($value) }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if ($job->required_columns['dob'])
        <div class="form-group">
            <label class="control-label required">@lang('modules.front.dob')</label>
            <input class="form-control form-control-lg dob" type="text" name="dob"
                placeholder="@lang('modules.front.dob')" autocomplete="none">
        </div>
    @endif
    @if ($job->required_columns['country'])
        <div class="row">
            <div class="col-md-12">
                <h6>
                    <strong class="required">
                        @lang('modules.front.country')
                    </strong>
                </h6>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select class="select2 countries" name="country" id="countryId">
                        <option value="0">@lang('modules.front.selectCountry')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select class="select2 states" name="state" id="stateId">
                        <option value="0">@lang('modules.front.selectState')</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input class="form-control" type="text" name="city" id="cityId" placeholder="@lang('modules.front.selectCity')" value="{{ !empty($application) ? $application->cover_letter : '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input class="form-control" type="number" name="zip_code" id="zipCode" placeholder="@lang('modules.front.zipCode')">
                </div>
            </div>
        </div>
    @endif
@endif
