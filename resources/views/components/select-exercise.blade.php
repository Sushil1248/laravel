

<select {{ $attributes->merge(['class'=>'js-data-example-ajax']) }}>
    <option value="">Select Exercise</option>
    @foreach( $exercises as $singleExercise )
    <option value="{{ jsencode_userdata($singleExercise->id) }}" {{ in_array($singleExercise->id , $selected) ? "selected" : "" }}>{{ $singleExercise->name }}</option>
    @endforeach
    {{-- @if( $exercises->where('is_steph_workout',1)->count() )
    <optgroup label="Steph Exercise">
    @foreach( $exercises->where('is_steph_workout',1) as $singleExercise )
    <option value="{{ jsencode_userdata($singleExercise->id) }}" {{ in_array($singleExercise->id , $selected) ? "selected" : "" }}>{{ $singleExercise->name }}</option>
    @endforeach
    </optgroup>
    @endif

    @if( $exercises->where('is_steph_workout',0)->count() )
    <optgroup label="Program Exercise">
    @foreach( $exercises->where('is_steph_workout',0) as $singleExercise )
    <option value="{{ jsencode_userdata($singleExercise->id) }}" {{ in_array($singleExercise->id , $selected) ? "selected" : "" }}>{{ $singleExercise->name }}</option>
    @endforeach
    </optgroup>
    @endif --}}

</select>
{{-- <select class="js-data-example-ajax"></select> --}}