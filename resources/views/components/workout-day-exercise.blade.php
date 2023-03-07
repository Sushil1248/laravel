@if( !empty($details) )
@php extract(  $details); @endphp
@endif
<div class="single-exercise {{ empty($is_super_set_div) ? '' : 'is-super-set' }}"> 
    <div class="left-side">
        <ul style="grid-template-columns: 1fr 1fr;padding-right: 0px !important;">
            <li>
                <p>Exercise<span class="required-field">*</span></p>
                <div class="input-group input-group-sm invoice-value">
                    @isset( $id )
                    <input type="hidden" name="details[][exercise][][exercise_detail]" value="{{ jsencode_userdata($id) }}">
                    @endisset
                    <x-select-exercise :exercises="$exercise->where('is_steph_workout',0)" :selected="isset($exercise_id) ? [$exercise_id] : []"  class="custom-select select-exercise" data-placeholder="Select exercises" name="details[][exercise][][exercise_id]" data-rule-required="true" data-msg-required="Workout exercise is required" />
                </div>
            </li>
            {{-- <li>
                <p>Information / Tips</p>
                <div class="input-group input-group-sm invoice-value">
                    <input type="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Information" name="details[][exercise][][information]" value="{{ $information_tips ?? '' }}"> 
                </div>
            </li> --}}

            <li>
                <p>Set Type<span class="required-field">*</span></p>
                <div class="input-group input-group-sm invoice-value">
                    <select class="custom-select exercise-type" name="details[][exercise][][exercise_type]" @empty($is_super_set_div) data-rule-required="true" @endempty data-msg-required="Set type is required">
                        <option value="">Select Set Type</option>
                        @foreach( config('constants.EXERCISE_TYPE') as $key => $singleExerciseType )
                            <option value="{{ $key }}" {{ !empty($exercise_type) && $exercise_type == $key ? 'selected' : '' }}>
                                {{ displayWorkoutType($key) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </li>
            <li>
                <p>Workout Type</p>
                <div class="custom-control custom-radio">
                    <input type="radio" name="details[][exercise][{{ $id ?? '' }}][set_type]" {{ !empty($set_type) && $set_type == 'reps' ? 'checked' : '' }} value="reps" class="custom-control-input workout-type">
                    <label class="custom-control-label">
                        Reps
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" name="details[][exercise][{{ $id ?? '' }}][set_type]" {{ !empty($set_type) && $set_type == 'time' ? 'checked' : '' }} value="time" class="custom-control-input workout-type">
                    <label class="custom-control-label">
                        Time Based
                    </label>
                </div>
            </li>

            <li>
                <p>Warm Up Type</p>
                <div class="custom-control custom-radio">
                    <input type="radio" name="details[][exercise][{{ $id ?? '' }}][warm_up_type]" {{ !empty($warm_up_type) && $warm_up_type == 'extended' ? 'checked' : '' }} value="extended" class="custom-control-input warm-up-type">
                    <label class="custom-control-label">
                        Extended
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" name="details[][exercise][{{ $id ?? '' }}][warm_up_type]" {{ !empty($warm_up_type) && $warm_up_type == 'single' ? 'checked' : '' }} value="single" class="custom-control-input warm-up-type">
                    <label class="custom-control-label">
                        Single
                    </label>
                </div>
            </li>
        </ul>
        <div class="all-workout-warmup">
            <!-- HTML from Ajax code -->
            @if( !empty($warmup_workout_set) )
                @foreach( $warmup_workout_set as $singleSet )
                    @include('admin.program-workout.workout-warmup',['includeData'   =>  $singleSet])
                @endforeach
            @endif
        </div>
        <div  style="position: absolute;bottom: -35px;margin: 0;right: -65px;">
            <!-- Show only in program workout -->
            {{-- @if( !empty($program) ) --}}
            <a class="add-exercise badge badge-primary" href="#">Add Exercise <i class="fas fa-plus"></i></a>
            {{-- @endif --}}
            
        </div>
    </div>
    <div class="all-workout-set">
        @if( !empty($workout_set) )
            @foreach( $workout_set as $singleSet )
                @include('admin.program-workout.workout-set',['includeData'   =>  $singleSet])
            @endforeach
        @else
            @include('admin.program-workout.workout-set')
        @endif
    </div>

    <!-- Show only in program workout -->
    {{-- @if( !empty($program) ) --}}
    <a class="remove-exercise badge badge-danger" href="#">Remove Exercise <i class="fas fa-minus"></i></a>
    {{-- @endif --}}
</div>

@if( !empty($super_set) )
    <x-workout-day-exercise :details="$super_set" :is-super-set-div="1" :program="$program"/>
@endif